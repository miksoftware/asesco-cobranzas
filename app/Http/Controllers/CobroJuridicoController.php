<?php

namespace App\Http\Controllers;

use App\Models\CobroJuridico;
use App\Models\CobroJuridicoDeposito;
use App\Models\CobroJuridicoGestion;
use App\Models\CobroJuridicoHistory;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CobroJuridicoController extends Controller
{
    public function index(Request $request)
    {
        $lastRecord = CobroJuridico::orderBy('id', 'desc')->first();
        $lastNo = $lastRecord && $lastRecord->no_consecutivo ? (int) preg_replace('/[^0-9]/', '', $lastRecord->no_consecutivo) : 0;
        $nextNoConsecutivo = 'CJ-' . str_pad($lastNo + 1, 6, '0', STR_PAD_LEFT);
        $cedula = $request->query('cedula', '');
        $departamentos = Department::where('is_active', true)
            ->with(['municipios' => function($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        return view('cobro_juridico.index', compact('nextNoConsecutivo', 'cedula', 'departamentos'));
    }

    public function list(Request $request)
    {
        $query = CobroJuridico::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('no_consecutivo', 'like', "%{$search}%")
                  ->orWhere('no_radicado', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('demandado_1', 'like', "%{$search}%")
                  ->orWhere('demandado_2', 'like', "%{$search}%")
                  ->orWhere('demandado_3', 'like', "%{$search}%")
                  ->orWhere('demandado_4', 'like', "%{$search}%")
                  ->orWhere('juzgado_conocimiento', 'like', "%{$search}%")
                  ->orWhere('estado_proceso', 'like', "%{$search}%")
                  ->orWhere('departamento', 'like', "%{$search}%")
                  ->orWhere('municipio', 'like', "%{$search}%")
                  ->orWhere('especialidad', 'like', "%{$search}%");
            });
        }

        $cobros = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        if ($request->ajax()) {
            return view('cobro_juridico.partials.list_table', compact('cobros'))->render();
        }

        return view('cobro_juridico.list', compact('cobros'));
    }

    public function historial(CobroJuridico $cobroJuridico)
    {
        return response()->json([
            'histories' => $cobroJuridico->histories()->with('user')->orderBy('created_at', 'desc')->get()
        ]);
    }

    public function show(CobroJuridico $cobroJuridico)
    {
        $cobroJuridico->load(['depositos', 'gestiones.user', 'histories.user']);
        $nextNoConsecutivo = $cobroJuridico->no_consecutivo;
        $cedula = $cobroJuridico->cedula ?? '';
        $departamentos = Department::where('is_active', true)
            ->with(['municipios' => function($q) {
                $q->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        return view('cobro_juridico.index', compact('cobroJuridico', 'nextNoConsecutivo', 'cedula', 'departamentos'));
    }

    private function logChanges($cobroJuridicoId, $seccion, $oldData, $newData)
    {
        $userId = Auth::id() ?? 1;
        $accion = empty($oldData) ? 'CREADO' : 'EDITADO';

        $cambiosAnteriores = [];
        $cambiosNuevos = [];

        foreach ($newData as $key => $value) {
            $oldValue = $oldData[$key] ?? null;
            if ($oldValue !== $value) {
                $cambiosAnteriores[$key] = $oldValue;
                $cambiosNuevos[$key] = $value;
            }
        }

        if (!empty($cambiosNuevos) || $accion === 'CREADO') {
            $camposStr = $accion === 'CREADO' ? 'Todos los campos' : count($cambiosNuevos) . ' campo(s)';
            
            CobroJuridicoHistory::create([
                'cobro_juridico_id' => $cobroJuridicoId,
                'user_id' => $userId,
                'seccion' => $seccion,
                'accion' => $accion,
                'campo' => $camposStr,
                'valor_anterior' => empty($cambiosAnteriores) ? null : json_encode($cambiosAnteriores),
                'valor_nuevo' => empty($cambiosNuevos) ? null : json_encode($cambiosNuevos),
            ]);
        }
    }

    public function saveSection1(Request $request)
    {
        $data = $request->except(['_token', 'cobro_juridico_id']);
        if ($request->filled('cedula')) {
            $data['cedula'] = $request->cedula;
        }
        
        if ($request->cobro_juridico_id) {
            $cobro = CobroJuridico::findOrFail($request->cobro_juridico_id);
            $oldData = $cobro->only(array_keys($data));
            $cobro->update($data);
            $cobro->update(['is_section1_locked' => true]);
            $this->logChanges($cobro->id, 'Datos Generales del Proceso', $oldData, $data);
        } else {
            if (empty($data['no_consecutivo'])) {
                $lastRecord = CobroJuridico::orderBy('id', 'desc')->first();
                $lastNo = $lastRecord && $lastRecord->no_consecutivo ? (int) preg_replace('/[^0-9]/', '', $lastRecord->no_consecutivo) : 0;
                $data['no_consecutivo'] = 'CJ-' . str_pad($lastNo + 1, 6, '0', STR_PAD_LEFT);
            }

            $cobro = CobroJuridico::create(array_merge($data, ['is_section1_locked' => true]));
            $this->logChanges($cobro->id, 'Datos Generales del Proceso', [], $data);
        }

        return response()->json([
            'success' => true,
            'cobro_juridico_id' => $cobro->id,
            'no_consecutivo' => $cobro->no_consecutivo,
            'no_radicado' => $cobro->no_radicado,
            'message' => 'Datos Generales del Proceso guardados correctamente.'
        ]);
    }

    public function saveSection2(Request $request)
    {
        $request->validate(['cobro_juridico_id' => 'required|exists:cobro_juridicos,id']);
        
        $cobro = CobroJuridico::findOrFail($request->cobro_juridico_id);
        $data = $request->except(['_token', 'cobro_juridico_id']);
        $oldData = $cobro->only(array_keys($data));
        
        if ($request->filled('cedula')) {
            $data['cedula'] = $request->cedula;
        } elseif (empty($cobro->cedula) && !empty($data['demandado_1'])) {
            if (preg_match('/\b\d{5,12}\b/', $data['demandado_1'], $matches)) {
                $data['cedula'] = $matches[0];
            }
        }
        
        $cobro->update($data);
        $cobro->update(['is_section2_locked' => true]);
        
        $this->logChanges($cobro->id, 'Datos Generales', $oldData, $data);

        return response()->json([
            'success' => true,
            'message' => 'Datos Generales guardados correctamente.'
        ]);
    }

    public function saveDepositos(Request $request)
    {
        $request->validate([
            'cobro_juridico_id' => 'required|exists:cobro_juridicos,id',
            'depositos' => 'array'
        ]);

        $cobro = CobroJuridico::findOrFail($request->cobro_juridico_id);
        
        if ($request->has('valor_total_proceso')) {
            $cobro->update(['valor_total_proceso' => $request->valor_total_proceso]);
        }

        CobroJuridicoHistory::create([
            'cobro_juridico_id' => $cobro->id,
            'user_id' => Auth::id() ?? 1,
            'seccion' => 'Relación Título Depósito Judicial',
            'accion' => 'ACTUALIZADO',
            'campo' => 'Tabla de Depósitos',
            'valor_anterior' => 'Registros previos',
            'valor_nuevo' => count($request->depositos ?? []) . ' registros'
        ]);

        $cobro->depositos()->delete();

        if ($request->depositos) {
            foreach ($request->depositos as $item) {
                $cobro->depositos()->create([
                    'fecha_descuento' => $item['fecha_descuento'] ?? null,
                    'valor' => $item['valor'] ?? null,
                    'fecha_consignacion' => $item['fecha_consignacion'] ?? null,
                    'reportado' => $item['reportado'] ?? false,
                    'aplicado' => $item['aplicado'] ?? false,
                    'soporte' => $item['soporte'] ?? null,
                ]);
            }
        }

        $cobro->update(['is_depositos_locked' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Títulos de depósito judicial guardados correctamente.'
        ]);
    }

    public function uploadDepositoSoporte(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('soportes_judiciales', 'public');
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se subió ningún archivo'], 400);
    }

    public function uploadGestionSoporte(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('soportes_gestiones_judiciales', 'public');
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se subió ningún archivo'], 400);
    }

    public function saveGestion(Request $request)
    {
        $request->validate([
            'cobro_juridico_id' => 'required|exists:cobro_juridicos,id',
            'gestion'           => 'required|string',
            'fecha_etapa'       => 'nullable|date',
            'etapa_procesal'    => 'nullable|string',
            'fecha_actividad'   => 'nullable|date',
            'actividad'         => 'nullable|string',
            'soporte'           => 'nullable|string',
        ]);

        $cobro = CobroJuridico::findOrFail($request->cobro_juridico_id);

        $gestion = CobroJuridicoGestion::create([
            'cobro_juridico_id' => $request->cobro_juridico_id,
            'user_id'           => Auth::id() ?? 1,
            'fecha_etapa'       => $request->fecha_etapa,
            'etapa_procesal'    => $request->etapa_procesal,
            'fecha_actividad'   => $request->fecha_actividad,
            'actividad'         => $request->actividad,
            'soporte'           => $request->soporte,
            'fecha_gestion'     => now(),
            'gestion'           => $request->gestion,
            'detalle'           => $request->gestion,
        ]);

        // Actualizar el cobro jurídico principal con los últimos datos de la gestión
        $cobro->update([
            'fecha_etapa'     => $request->fecha_etapa,
            'etapa_procesal'  => $request->etapa_procesal,
            'fecha_actividad' => $request->fecha_actividad,
            'actividad'       => $request->actividad,
        ]);

        CobroJuridicoHistory::create([
            'cobro_juridico_id' => $request->cobro_juridico_id,
            'user_id'           => Auth::id() ?? 1,
            'seccion'           => 'Gestiones de Cobro Jurídico',
            'accion'            => 'CREADO',
            'campo'             => 'Gestión',
            'valor_anterior'    => null,
            'valor_nuevo'       => $request->gestion . ($request->etapa_procesal ? ' (Etapa: ' . $request->etapa_procesal . ')' : ''),
        ]);

        return response()->json([
            'success' => true,
            'gestion' => $gestion->load('user'),
            'message' => 'Gestión agregada correctamente.'
        ]);
    }

    public function unlockSection(Request $request, CobroJuridico $cobroJuridico)
    {
        $request->validate([
            'section' => 'required|in:1,2,3'
        ]);

        $field = '';
        $sectionName = '';
        if ($request->section == '1') {
            $field = 'is_section1_locked';
            $sectionName = 'Datos Generales del Proceso';
        } elseif ($request->section == '2') {
            $field = 'is_section2_locked';
            $sectionName = 'Datos Generales';
        } elseif ($request->section == '3') {
            $field = 'is_depositos_locked';
            $sectionName = 'Relación Título Depósito Judicial';
        }

        if ($field) {
            $cobroJuridico->update([$field => false]);
            
            CobroJuridicoHistory::create([
                'cobro_juridico_id' => $cobroJuridico->id,
                'user_id' => Auth::id() ?? 1,
                'seccion' => $sectionName,
                'accion' => 'DESBLOQUEADO',
                'campo' => 'Estado Sección',
                'valor_anterior' => 'Bloqueado',
                'valor_nuevo' => 'Desbloqueado (Edición)'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sección desbloqueada para edición.'
        ]);
    }

    /**
     * Consultar historial de procesos judiciales asociados a una cédula en el sistema Rama Judicial.
     */
    public function consultaJudicial(Request $request, $cedula)
    {
        $cedula = trim($cedula);
        if (empty($cedula)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debe proporcionar un número de cédula válido.',
                'cedula' => '',
                'total_procesos' => 0,
                'data' => []
            ], 400);
        }

        $baseUrl = rtrim(config('services.rama_judicial.url', env('RAMA_JUDICIAL_API_URL', 'http://172.30.10.250:8010')), '/');
        $primaryUrl = "{$baseUrl}/api/procesos/cedula/" . urlencode($cedula);

        try {
            $response = Http::timeout(7)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($primaryUrl);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            if ($response->status() === 404) {
                $body = $response->json();
                if (is_array($body) && isset($body['status']) && $body['status'] === 'not_found') {
                    return response()->json($body);
                }
            }
        } catch (\Exception $e) {
            Log::warning("Consulta Rama Judicial primaria ({$primaryUrl}) falló: " . $e->getMessage());
        }

        // Fallback para desarrollo local si la ruta aún no está en el servidor remoto
        if (app()->environment('local')) {
            $fallbackBase = rtrim(env('RAMA_JUDICIAL_FALLBACK_URL', 'http://127.0.0.1:8000'), '/');
            if ($fallbackBase !== $baseUrl) {
                try {
                    $fallbackUrl = "{$fallbackBase}/api/procesos/cedula/" . urlencode($cedula);
                    $fbResponse = Http::timeout(4)
                        ->withHeaders(['Accept' => 'application/json'])
                        ->get($fallbackUrl);

                    if ($fbResponse->successful()) {
                        return response()->json($fbResponse->json());
                    }

                    if ($fbResponse->status() === 404) {
                        $fbBody = $fbResponse->json();
                        if (is_array($fbBody) && isset($fbBody['status'])) {
                            return response()->json($fbBody);
                        }
                    }
                } catch (\Exception $e) {
                    Log::info("Fallback local Rama Judicial falló: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'status' => 'not_found',
            'message' => "No se encontraron procesos judiciales asociados a la cédula '{$cedula}'.",
            'cedula' => $cedula,
            'total_procesos' => 0,
            'data' => []
        ]);
    }
}
