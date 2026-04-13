<?php

namespace App\Http\Controllers;

use App\Models\ConsultaResult;
use App\Models\EpsSystem;
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
}
