<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Retencion;
use App\Models\RetencionAbono;
use App\Models\RetencionGestion;
use App\Models\RetencionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetencionController extends Controller
{
    public function index()
    {
        $gestores = User::select('id', 'name')->get();
        // Carga una retención vacía por defecto
        return view('retenciones.index', compact('gestores'));
    }

    public function list(Request $request)
    {
        $query = Retencion::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('no_radicacion', 'like', "%{$search}%")
                  ->orWhere('cedula_tt', 'like', "%{$search}%")
                  ->orWhere('nombre_sujeto_retencion', 'like', "%{$search}%");
            });
        }

        $retenciones = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        if ($request->ajax()) {
            return view('retenciones.partials.list_table', compact('retenciones'))->render();
        }

        return view('retenciones.list', compact('retenciones'));
    }

    public function historial(Retencion $retencion)
    {
        return response()->json([
            'histories' => $retencion->histories()->with('user')->orderBy('created_at', 'desc')->get()
        ]);
    }

    public function show(Retencion $retencion)
    {
        $gestores = User::select('id', 'name')->get();
        $retencion->load(['abonos', 'gestiones.user', 'histories.user']);
        return view('retenciones.index', compact('gestores', 'retencion'));
    }

    private function logChanges($retencionId, $seccion, $oldData, $newData)
    {
        $userId = Auth::id() ?? 1;
        $accion = empty($oldData) ? 'CREADO' : 'EDITADO';

        $cambiosAnteriores = [];
        $cambiosNuevos = [];

        foreach ($newData as $key => $value) {
            $oldValue = $oldData[$key] ?? null;
            // Tratar strings vacíos y nulos como equivalentes para evitar falsos positivos
            $oldValStr = $oldValue === null ? '' : (string)$oldValue;
            $newValStr = $value === null ? '' : (string)$value;
            
            if ($oldValStr !== $newValStr && $key !== 'is_section1_locked' && $key !== 'is_section2_locked' && $key !== 'is_abonos_locked') {
                $cambiosAnteriores[$key] = $oldValue;
                $cambiosNuevos[$key] = $value;
            }
        }

        if (!empty($cambiosNuevos) || $accion === 'CREADO') {
            $camposStr = $accion === 'CREADO' ? 'Todos los campos' : count($cambiosNuevos) . ' campo(s)';
            
            RetencionHistory::create([
                'retencion_id' => $retencionId,
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
        $data = $request->except(['_token', 'retencion_id']);
        
        if ($request->retencion_id) {
            $retencion = Retencion::findOrFail($request->retencion_id);
            $oldData = $retencion->only(array_keys($data));
            $retencion->update($data);
            $retencion->update(['is_section1_locked' => true]);
            $this->logChanges($retencion->id, 'Datos Generales', $oldData, $data);
        } else {
            $retencion = Retencion::create(array_merge($data, ['is_section1_locked' => true]));
            $this->logChanges($retencion->id, 'Datos Generales', [], $data);
        }

        return response()->json([
            'success' => true,
            'retencion_id' => $retencion->id,
            'message' => 'Sección 1 guardada correctamente.'
        ]);
    }

    public function saveSection2(Request $request)
    {
        $request->validate(['retencion_id' => 'required|exists:retencions,id']);
        
        $retencion = Retencion::findOrFail($request->retencion_id);
        $data = $request->except(['_token', 'retencion_id']);
        $oldData = $retencion->only(array_keys($data));
        
        $retencion->update($data);
        $retencion->update(['is_section2_locked' => true]);
        
        $this->logChanges($retencion->id, 'Datos Generales de la Retención', $oldData, $data);

        return response()->json([
            'success' => true,
            'message' => 'Sección 2 guardada correctamente.'
        ]);
    }

    public function saveAbonos(Request $request)
    {
        $request->validate([
            'retencion_id' => 'required|exists:retencions,id',
            'abonos' => 'array'
        ]);

        $retencion = Retencion::findOrFail($request->retencion_id);
        
        // Simplemente borramos y recreamos los abonos para simplificar el historial
        // o podemos hacer un sync. Para el historial general:
        RetencionHistory::create([
            'retencion_id' => $retencion->id,
            'user_id' => Auth::id() ?? 1,
            'seccion' => 'Relación de Descuentos por Nómina',
            'accion' => 'ACTUALIZADO',
            'campo' => 'Tabla de Abonos',
            'valor_anterior' => 'Registros previos',
            'valor_nuevo' => count($request->abonos ?? []) . ' registros'
        ]);

        $retencion->abonos()->delete();

        if ($request->abonos) {
            foreach ($request->abonos as $abonoData) {
                $retencion->abonos()->create([
                    'fecha_descuento' => $abonoData['fecha_descuento'] ?? null,
                    'valor' => $abonoData['valor'] ?? null,
                    'fecha_consignacion' => $abonoData['fecha_consignacion'] ?? null,
                    'reportado' => $abonoData['reportado'] ?? false,
                    'aplicado' => $abonoData['aplicado'] ?? false,
                ]);
            }
        }

        $retencion->update(['is_abonos_locked' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Abonos guardados correctamente.'
        ]);
    }

    public function saveGestion(Request $request)
    {
        $request->validate([
            'retencion_id' => 'required|exists:retencions,id',
            'detalle' => 'required|string'
        ]);

        $gestion = RetencionGestion::create([
            'retencion_id' => $request->retencion_id,
            'user_id' => Auth::id() ?? 1,
            'detalle' => $request->detalle
        ]);

        RetencionHistory::create([
            'retencion_id' => $request->retencion_id,
            'user_id' => Auth::id() ?? 1,
            'seccion' => 'Gestiones de Retenciones',
            'accion' => 'CREADO',
            'campo' => 'Gestión',
            'valor_anterior' => null,
            'valor_nuevo' => $request->detalle
        ]);

        return response()->json([
            'success' => true,
            'gestion' => $gestion->load('user'),
            'message' => 'Gestión agregada correctamente.'
        ]);
    }

    public function unlockSection(Request $request, Retencion $retencion)
    {
        $request->validate([
            'section' => 'required|in:1,2,3'
        ]);

        $field = '';
        $sectionName = '';
        if ($request->section == '1') {
            $field = 'is_section1_locked';
            $sectionName = 'Datos Generales';
        } elseif ($request->section == '2') {
            $field = 'is_section2_locked';
            $sectionName = 'Datos Generales de la Retención';
        } elseif ($request->section == '3') {
            $field = 'is_abonos_locked';
            $sectionName = 'Relación de Descuentos por Nómina';
        }

        if ($field) {
            $retencion->update([$field => false]);
            
            RetencionHistory::create([
                'retencion_id' => $retencion->id,
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
}
