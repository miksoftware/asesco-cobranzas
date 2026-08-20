<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\EmpresaTarifa;
use App\Models\EmpresaCanal;
use App\Models\EmpresaLineamiento;
use App\Models\EmpresaLineamientoPorcentaje;
use App\Models\EmpresaLineamientoTramo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::orderBy('nombre')->get();
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        $empresa = Empresa::create(['nombre' => $request->nombre]);

        return response()->json(['id' => $empresa->id, 'message' => 'Empresa creada exitosamente.']);
    }

    public function updateNombre(Request $request, Empresa $empresa)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $empresa->update(['nombre' => $request->nombre]);
        return response()->json(['message' => 'Nombre actualizado.']);
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['tarifas', 'canales', 'lineamientos.porcentaje', 'lineamientos.tramos']);
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $empresa->load(['tarifas', 'canales', 'lineamientos.porcentaje', 'lineamientos.tramos']);
        return view('empresas.edit', compact('empresa'));
    }

    // ── Paso 2: Tarifas ──────────────────────────────────────────────────────

    public function saveTarifas(Request $request, Empresa $empresa)
    {
        $request->validate([
            'tarifas'                       => 'required|array|min:1',
            'tarifas.*.nombre_tramo'        => 'required|string|max:100',
            'tarifas.*.porcentaje_vigente'  => 'required|numeric|min:0|max:100',
            'tarifas.*.porcentaje_castigada'=> 'required|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $empresa) {
            $empresa->tarifas()->delete();
            foreach ($request->tarifas as $i => $t) {
                EmpresaTarifa::create([
                    'empresa_id'            => $empresa->id,
                    'nombre_tramo'          => $t['nombre_tramo'],
                    'dias_desde'            => $t['dias_desde'] ?? null,
                    'dias_hasta'            => $t['dias_hasta'] ?? null,
                    'porcentaje_vigente'    => $t['porcentaje_vigente'],
                    'porcentaje_castigada'  => $t['porcentaje_castigada'],
                    'orden'                 => $i,
                ]);
            }
        });

        return response()->json(['message' => 'Tarifas guardadas exitosamente.']);
    }

    // ── Paso 3: Canales ───────────────────────────────────────────────────────

    public function saveCanales(Request $request, Empresa $empresa)
    {
        $request->validate([
            'canales'                  => 'required|array|min:1',
            'canales.*.nombre_canal'   => 'required|string|max:150',
            'canales.*.numero_canal'   => 'nullable|string|max:50',
            'canales.*.medio_pago'     => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($request, $empresa) {
            $empresa->canales()->delete();
            foreach ($request->canales as $i => $c) {
                EmpresaCanal::create([
                    'empresa_id'   => $empresa->id,
                    'nombre_canal' => $c['nombre_canal'],
                    'numero_canal' => $c['numero_canal'] ?? null,
                    'medio_pago'   => $c['medio_pago'],
                    'orden'        => $i,
                ]);
            }
        });

        return response()->json(['message' => 'Canales guardados exitosamente.']);
    }

    // ── Paso 4: Lineamientos ──────────────────────────────────────────────────

    public function saveLineamientos(Request $request, Empresa $empresa)
    {
        $request->validate([
            'lineamientos'              => 'required|array|min:1',
            'lineamientos.*.tipo'       => 'required|in:porcentaje,tramo',
            'lineamientos.*.concepto'   => 'required|string|max:150',
            'lineamientos.*.is_active'  => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($request, $empresa) {
            // Eliminar lineamientos anteriores en cascada
            $empresa->lineamientos()->each(function ($lin) {
                $lin->porcentaje()->delete();
                $lin->tramos()->delete();
                $lin->delete();
            });

            // Asegurar que solo uno sea activo: el primero marcado como activo gana
            $activeSet = false;
            foreach ($request->lineamientos as $i => $lin) {
                $isActive = !empty($lin['is_active']) && !$activeSet;
                if ($isActive) $activeSet = true;

                $lineamiento = EmpresaLineamiento::create([
                    'empresa_id' => $empresa->id,
                    'tipo'       => $lin['tipo'],
                    'concepto'   => $lin['concepto'],
                    'orden'      => $i,
                    'is_active'  => $isActive,
                ]);

                if ($lin['tipo'] === 'porcentaje') {
                    EmpresaLineamientoPorcentaje::create([
                        'lineamiento_id'       => $lineamiento->id,
                        'porcentaje_vigente'   => $lin['porcentaje_vigente'] ?? 0,
                        'porcentaje_castigado' => $lin['porcentaje_castigado'] ?? 0,
                    ]);
                } else {
                    if (!empty($lin['tramos_vigente'])) {
                        foreach ($lin['tramos_vigente'] as $j => $tramo) {
                            EmpresaLineamientoTramo::create([
                                'lineamiento_id' => $lineamiento->id,
                                'nombre_tramo'   => $tramo['nombre_tramo'],
                                'tipo_cartera'   => 'vigente',
                                'porcentaje'     => $tramo['porcentaje'],
                                'orden'          => $j,
                            ]);
                        }
                    }
                    if (!empty($lin['tramos_castigado'])) {
                        foreach ($lin['tramos_castigado'] as $j => $tramo) {
                            EmpresaLineamientoTramo::create([
                                'lineamiento_id' => $lineamiento->id,
                                'nombre_tramo'   => $tramo['nombre_tramo'],
                                'tipo_cartera'   => 'castigado',
                                'porcentaje'     => $tramo['porcentaje'],
                                'orden'          => $j,
                            ]);
                        }
                    }
                }
            }
        });

        return response()->json(['message' => 'Lineamientos guardados exitosamente.']);
    }

    /**
     * Activa un lineamiento y desactiva todos los demás de la misma empresa.
     */
    public function activarLineamiento(Empresa $empresa, EmpresaLineamiento $lineamiento)
    {
        DB::transaction(function () use ($empresa, $lineamiento) {
            $empresa->lineamientos()->update(['is_active' => false]);
            $lineamiento->update(['is_active' => true]);
        });
        return response()->json(['message' => 'Lineamiento activado.', 'lineamiento_id' => $lineamiento->id]);
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada.');
    }

    public function toggle(Empresa $empresa)
    {
        $empresa->update(['is_active' => !$empresa->is_active]);
        return response()->json(['is_active' => $empresa->is_active]);
    }

    // ── API: obtener datos de una empresa para edición ─────────────────────

    public function getData(Empresa $empresa)
    {
        $empresa->load(['tarifas', 'canales', 'lineamientos.porcentaje', 'lineamientos.tramos']);
        return response()->json($empresa);
    }
}
