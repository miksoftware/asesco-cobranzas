<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\ConsultaResult;
use App\Models\EpsSystem;
use App\Models\Tercero;
use App\Services\EpsConsultaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    public function __construct(private EpsConsultaService $service) {}

    public function index()
    {
        $systemCount = EpsSystem::active()->count();
        return view('consultas.index', compact('systemCount'));
    }

    /**
     * Consulta concurrente a todos los sistemas EPS.
     * Retorna JSON para que el frontend lo renderice dinámicamente.
     */
    public function consultar(Request $request): JsonResponse
    {
        $request->validate([
            'cedula' => 'required|string|regex:/^[0-9]+$/|min:5|max:15',
        ]);

        $cedula  = $request->input('cedula');
        $results = $this->service->consultarCedula($cedula);

        // Buscar datos del tercero (titular) en la tabla de asignación
        $tercero = Tercero::where('referencia', $cedula)
            ->where('calidad', 'TT')
            ->first();

        $terceroInfo = null;
        if ($tercero) {
            $terceroInfo = [
                'nombre'  => $tercero->nombre_tercero,
                'empresa' => $tercero->empresa,
            ];
        }

        // Guardar resultados en BD
        foreach ($results as $slug => $result) {
            ConsultaResult::create([
                'cedula'        => $cedula,
                'eps_system_id' => $result['system']->id,
                'user_id'       => auth()->id(),
                'data'          => $result['data'],
                'found'         => $result['success'],
                'error'         => $result['error'],
            ]);
        }

        // Formatear respuesta
        $formatted = collect($results)->map(fn($r, $slug) => [
            'slug'    => $slug,
            'name'    => $r['system']->name,
            'found'   => $r['success'],
            'data'    => $r['data'],
            'error'   => $r['error'],
        ])->values();

        return response()->json([
            'cedula'  => $cedula,
            'results' => $formatted,
            'total'   => $formatted->count(),
            'found'   => $formatted->where('found', true)->count(),
            'tercero' => $terceroInfo,
        ]);
    }

    /**
     * Historial de consultas recientes.
     */
    public function historial(Request $request): JsonResponse
    {
        $consultas = ConsultaResult::with('epsSystem')
            ->where('user_id', auth()->id())
            ->when($request->cedula, fn($q, $c) => $q->where('cedula', $c))
            ->latest()
            ->limit(100)
            ->get()
            ->groupBy('cedula')
            ->map(function ($group, $cedula) {
                return [
                    'cedula'       => $cedula,
                    'last_consulta'=> $group->first()->created_at->toIso8601String(),
                    'systems'      => $group->map(fn($r) => [
                        'name'  => $r->epsSystem->name ?? 'Desconocido',
                        'found' => $r->found,
                    ])->values(),
                ];
            })->values()->take(20);

        return response()->json($consultas);
    }

    /**
     * Obtener retenciones de un tercero por cédula.
     */
    public function retencionesPorCedula(string $cedula): JsonResponse
    {
        $retenciones = \App\Models\Retencion::where('cedula_tt', $cedula)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($retenciones);
    }

    /**
     * Obtener comentarios de una cédula específica.
     */
    public function comentariosPorCedula(string $cedula): JsonResponse
    {
        $comentarios = Comentario::where('cedula', $cedula)
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->get();

        return response()->json($comentarios);
    }

    /**
     * Crear un nuevo comentario desde Gestiones.
     */
    public function crearComentario(Request $request): JsonResponse
    {
        $request->validate([
            'cedula'         => 'required|string|regex:/^[0-9]+$/|min:5|max:15',
            'comentario'     => 'required|string|min:3|max:2000',
            'canal'          => 'required|string',
            'tipo_contacto'  => 'required|string',
            'efecto_gestion' => 'required|string',
            'accion_cobro'   => 'required|string',
        ]);

        // Buscar nombre y empresa del tercero
        $tercero = Tercero::where('referencia', $request->cedula)
            ->where('calidad', 'TT')
            ->first();

        $now = now();

        $comentario = Comentario::create([
            'fecha'          => $now->format('Y-m-d'),
            'hora'           => $now->format('g:i a'),
            'gestor'         => mb_strtoupper(auth()->user()->name),
            'comentario'     => $request->comentario,
            'canal'          => mb_strtoupper($request->canal),
            'tipo_contacto'  => mb_strtoupper($request->tipo_contacto),
            'efecto_gestion' => $request->efecto_gestion,
            'accion_cobro'   => mb_strtoupper($request->accion_cobro),
            'cedula'         => $request->cedula,
            'nombre'         => $tercero?->nombre_tercero ?? '—',
            'empresa'        => $tercero?->empresa ?? '—',
            'user_id'        => auth()->id(),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Comentario guardado correctamente.',
            'comentario' => $comentario,
        ]);
    }

    /**
     * Obtener teléfonos y correos de un tercero por cédula (referencia).
     */
    public function telefonosPorCedula(string $cedula): JsonResponse
    {
        $telefonos = Tercero::with('modifiedByUser:id,name')
            ->where('referencia', $cedula)
            ->whereIn('tipo_dato', ['celular', 'fijo'])
            ->orderBy('calidad')
            ->orderBy('cedula_tercero')
            ->get();

        $correos = Tercero::with('modifiedByUser:id,name')
            ->where('referencia', $cedula)
            ->where('tipo_dato', 'correo')
            ->orderBy('calidad')
            ->orderBy('cedula_tercero')
            ->get();

        return response()->json([
            'telefonos' => $telefonos,
            'correos'   => $correos,
        ]);
    }

    /**
     * Agregar un nuevo teléfono o correo desde Gestiones.
     */
    public function crearTelefono(Request $request): JsonResponse
    {
        $request->validate([
            'referencia'      => 'required|string|regex:/^[0-9]+$/|min:5|max:15',
            'cedula_tercero'  => 'required|string|min:3|max:20',
            'nombre_tercero'  => 'required|string|min:2|max:255',
            'calidad'         => 'required|string|max:50',
            'dato'            => 'required|string|min:3|max:255',
            'tipo_dato'       => 'required|string|in:celular,fijo,correo',
            'fuente'          => 'required|string|max:100',
            'notificar'       => 'boolean',
        ]);

        // Verificar duplicado
        $exists = Tercero::where('referencia', $request->referencia)
            ->where('cedula_tercero', $request->cedula_tercero)
            ->where('dato', $request->dato)
            ->where('tipo_dato', $request->tipo_dato)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Este dato ya existe para este tercero.',
            ], 422);
        }

        $tercero = Tercero::create([
            'referencia'      => $request->referencia,
            'cedula_tercero'  => $request->cedula_tercero,
            'nombre_tercero'  => mb_strtoupper($request->nombre_tercero),
            'calidad'         => mb_strtoupper($request->calidad),
            'empresa'         => mb_strtoupper($request->fuente),
            'dato'            => $request->dato,
            'tipo_dato'       => $request->tipo_dato,
            'fuente'          => mb_strtoupper($request->fuente),
            'notificar'       => $request->boolean('notificar', false),
            'user_id'         => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dato agregado correctamente.',
            'tercero' => $tercero,
        ]);
    }

    /**
     * Toggle notificar (SMS/Correo) de un tercero.
     */
    public function toggleNotificar(Tercero $tercero): JsonResponse
    {
        $tercero->update([
            'notificar'   => !$tercero->notificar,
            'modified_by' => auth()->id(),
            'modified_at' => now(),
        ]);

        return response()->json([
            'success'   => true,
            'notificar' => $tercero->notificar,
        ]);
    }

    /**
     * Editar un registro de teléfono/correo.
     */
    public function editarTelefono(Request $request, Tercero $tercero): JsonResponse
    {
        $request->validate([
            'dato'            => 'required|string|min:3|max:255',
            'tipo_dato'       => 'required|string|in:celular,fijo,correo',
            'calidad'         => 'required|string|max:50',
            'fuente'          => 'nullable|string|max:100',
            'nombre_tercero'  => 'required|string|min:2|max:255',
            'cedula_tercero'  => 'required|string|min:3|max:20',
            'notificar'       => 'boolean',
        ]);

        $tercero->update([
            'dato'            => $request->dato,
            'tipo_dato'       => $request->tipo_dato,
            'calidad'         => mb_strtoupper($request->calidad),
            'fuente'          => $request->fuente ? mb_strtoupper($request->fuente) : null,
            'nombre_tercero'  => mb_strtoupper($request->nombre_tercero),
            'cedula_tercero'  => $request->cedula_tercero,
            'notificar'       => $request->boolean('notificar', false),
            'modified_by'     => auth()->id(),
            'modified_at'     => now(),
        ]);

        $tercero->load('modifiedByUser:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Registro actualizado correctamente.',
            'tercero' => $tercero,
        ]);
    }
    /**
     * Obtener adjuntos de un tercero por cédula.
     */
    public function adjuntosPorCedula(string $cedula): JsonResponse
    {
        $adjuntos = \App\Models\GestionAdjunto::where('cedula', $cedula)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($adjuntos);
    }

    /**
     * Subir adjunto para Gestiones
     */
    public function uploadAdjunto(Request $request): JsonResponse
    {
        $request->validate([
            'cedula' => 'required|string',
            'comentario' => 'nullable|string|max:1000',
            'soporte' => 'required|file|mimes:pdf,jpg,jpeg,png,zip,rar,doc,docx,xls,xlsx|max:5120', // 5MB Max
        ]);

        $filePath = null;
        if ($request->hasFile('soporte')) {
            $file = $request->file('soporte');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('soportes_gestiones', $fileName, 'public');
        }

        $adjunto = \App\Models\GestionAdjunto::create([
            'cedula' => $request->cedula,
            'comentario' => $request->comentario,
            'archivo_path' => $filePath,
            'user_id' => auth()->id(),
        ]);

        $adjunto->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Adjunto subido correctamente.',
            'adjunto' => $adjunto
        ]);
    }

    /**
     * Eliminar adjunto
     */
    public function deleteAdjunto(\App\Models\GestionAdjunto $adjunto): JsonResponse
    {
        if ($adjunto->archivo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($adjunto->archivo_path);
        }

        $adjunto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Adjunto eliminado correctamente.'
        ]);
    }

    /**
     * Obtener pagos (Retención + Directos) por cédula ordenados del más reciente al más antiguo.
     */
    public function pagosPorCedula(string $cedula): JsonResponse
    {
        // 1. Abonos de Retenciones para esta cédula
        $retencionAbonos = \App\Models\RetencionAbono::whereHas('retencion', function ($q) use ($cedula) {
                $q->where('cedula_tt', $cedula);
            })
            ->with('retencion:id,no_radicacion,cedula_tt')
            ->get()
            ->map(function ($abono) {
                $noRadicacion = $abono->retencion->no_radicacion ?? $abono->retencion_id;
                $fDescuento = $abono->fecha_descuento ? substr((string)$abono->fecha_descuento, 0, 10) : null;
                $fConsignacion = $abono->fecha_consignacion ? substr((string)$abono->fecha_consignacion, 0, 10) : null;

                return [
                    'id'                => 'ret_' . $abono->id,
                    'db_id'             => $abono->id,
                    'cedula'            => $abono->retencion->cedula_tt ?? '',
                    'fecha_descuento'   => $fDescuento,
                    'valor'             => (float) $abono->valor,
                    'fecha_consignacion'=> $fConsignacion,
                    'reportado'         => (bool) $abono->reportado,
                    'aplicado'          => (bool) $abono->aplicado,
                    'soporte'           => $abono->soporte,
                    'es_retencion'      => true,
                    'locked'            => true,
                    'origen'            => "Retención #{$noRadicacion}",
                    'no_radicacion'     => $noRadicacion,
                ];
            });

        // 2. Pagos directos creados en Gestiones para esta cédula
        $gestionPagos = \App\Models\GestionPago::where('cedula', $cedula)
            ->get()
            ->map(function ($pago) {
                $fDescuento = $pago->fecha_descuento ? substr((string)$pago->fecha_descuento, 0, 10) : null;
                $fConsignacion = $pago->fecha_consignacion ? substr((string)$pago->fecha_consignacion, 0, 10) : null;

                return [
                    'id'                => 'dir_' . $pago->id,
                    'db_id'             => $pago->id,
                    'cedula'            => $pago->cedula,
                    'fecha_descuento'   => $fDescuento,
                    'valor'             => (float) $pago->valor,
                    'fecha_consignacion'=> $fConsignacion,
                    'reportado'         => (bool) $pago->reportado,
                    'aplicado'          => (bool) $pago->aplicado,
                    'soporte'           => $pago->soporte,
                    'es_retencion'      => false,
                    'locked'            => true,
                    'origen'            => 'Pago Directo',
                    'no_radicacion'     => null,
                ];
            });

        // Combinar y ordenar de más reciente a más viejo por fecha_descuento
        $combined = $retencionAbonos->concat($gestionPagos)->sortByDesc(function ($item) {
            return $item['fecha_descuento'] ?? '0000-00-00';
        })->values();

        return response()->json($combined);
    }

    /**
     * Guardar o actualizar un pago directo en Gestiones.
     */
    public function savePago(Request $request): JsonResponse
    {
        $request->validate([
            'id'                 => 'nullable',
            'db_id'              => 'nullable',
            'cedula'             => 'required|string',
            'fecha_descuento'    => 'nullable|date',
            'valor'              => 'nullable|numeric',
            'fecha_consignacion' => 'nullable|date',
            'reportado'          => 'boolean',
            'aplicado'           => 'boolean',
            'soporte'            => 'nullable|string',
        ]);

        $pago = null;
        if ($request->filled('db_id')) {
            $pago = \App\Models\GestionPago::where('id', $request->db_id)->where('cedula', $request->cedula)->first();
        } elseif ($request->filled('id') && str_starts_with((string)$request->id, 'dir_')) {
            $dbId = (int) str_replace('dir_', '', $request->id);
            $pago = \App\Models\GestionPago::where('id', $dbId)->where('cedula', $request->cedula)->first();
        }

        if ($pago) {
            $pago->update([
                'fecha_descuento'   => $request->fecha_descuento,
                'valor'             => $request->valor,
                'fecha_consignacion'=> $request->fecha_consignacion,
                'reportado'         => $request->boolean('reportado'),
                'aplicado'          => $request->boolean('aplicado'),
                'soporte'           => $request->soporte,
            ]);
        } else {
            $pago = \App\Models\GestionPago::create([
                'cedula'            => $request->cedula,
                'fecha_descuento'   => $request->fecha_descuento,
                'valor'             => $request->valor,
                'fecha_consignacion'=> $request->fecha_consignacion,
                'reportado'         => $request->boolean('reportado'),
                'aplicado'          => $request->boolean('aplicado'),
                'soporte'           => $request->soporte,
                'user_id'           => auth()->id(),
            ]);
        }

        $fDescuento = $pago->fecha_descuento ? substr((string)$pago->fecha_descuento, 0, 10) : null;
        $fConsignacion = $pago->fecha_consignacion ? substr((string)$pago->fecha_consignacion, 0, 10) : null;

        return response()->json([
            'success' => true,
            'message' => 'Pago guardado correctamente.',
            'pago'    => [
                'id'                => 'dir_' . $pago->id,
                'db_id'             => $pago->id,
                'cedula'            => $pago->cedula,
                'fecha_descuento'   => $fDescuento,
                'valor'             => (float) $pago->valor,
                'fecha_consignacion'=> $fConsignacion,
                'reportado'         => (bool) $pago->reportado,
                'aplicado'          => (bool) $pago->aplicado,
                'soporte'           => $pago->soporte,
                'es_retencion'      => false,
                'origen'            => 'Pago Directo',
            ]
        ]);
    }

    /**
     * Subir soporte para pago directo en Gestiones.
     */
    public function uploadPagoSoporte(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('soportes_pagos', $fileName, 'public');

        return response()->json([
            'success' => true,
            'path'    => $path,
        ]);
    }

    /**
     * Eliminar un pago directo de Gestiones.
     */
    public function deletePago(\App\Models\GestionPago $pago): JsonResponse
    {
        if ($pago->soporte) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pago->soporte);
        }

        $pago->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pago eliminado correctamente.'
        ]);
    }
}
