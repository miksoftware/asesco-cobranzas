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
}
