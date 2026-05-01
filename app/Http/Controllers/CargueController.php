<?php

namespace App\Http\Controllers;

use App\Imports\ComentariosImport;
use App\Imports\TercerosImport;
use App\Models\Comentario;
use App\Models\Tercero;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CargueController extends Controller
{
    /**
     * Vista principal del módulo de cargues - Reporte de Teléfonos.
     */
    public function telefonos()
    {
        $stats = [
            'total_registros' => Tercero::count(),
            'total_titulares' => Tercero::distinct('referencia')->count('referencia'),
            'total_terceros'  => Tercero::distinct('cedula_tercero')->count('cedula_tercero'),
            'total_empresas'  => Tercero::distinct('empresa')->count('empresa'),
        ];

        return view('cargues.telefonos', compact('stats'));
    }

    /**
     * Importar archivo XLSX de terceros.
     */
    public function importar(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            $import = new TercerosImport();
            Excel::import($import, $request->file('archivo'));

            $resultado = $import->getResultado();

            return response()->json([
                'success'    => true,
                'message'    => "Importación completada: {$resultado['nuevos']} nuevos, {$resultado['duplicados']} duplicados omitidos, {$resultado['errores']} con errores.",
                'nuevos'     => $resultado['nuevos'],
                'duplicados' => $resultado['duplicados'],
                'errores'    => $resultado['errores'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Listar registros de terceros con filtros (para la tabla).
     */
    public function listar(Request $request): JsonResponse
    {
        $query = Tercero::query();

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('referencia', 'like', "%{$buscar}%")
                  ->orWhere('cedula_tercero', 'like', "%{$buscar}%")
                  ->orWhere('nombre_tercero', 'like', "%{$buscar}%")
                  ->orWhere('empresa', 'like', "%{$buscar}%")
                  ->orWhere('dato', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('calidad')) {
            $query->where('calidad', $request->input('calidad'));
        }

        if ($request->filled('tipo_dato')) {
            $query->where('tipo_dato', $request->input('tipo_dato'));
        }

        $registros = $query->orderBy('referencia')
            ->orderBy('cedula_tercero')
            ->orderBy('empresa')
            ->paginate(50);

        return response()->json($registros);
    }

    // ─── Comentarios ─────────────────────────────────────────────

    /**
     * Vista del módulo de cargues - Reporte Comentarios.
     */
    public function comentarios()
    {
        $stats = [
            'total_comentarios' => Comentario::count(),
            'total_cedulas'     => Comentario::distinct('cedula')->count('cedula'),
            'total_gestores'    => Comentario::distinct('gestor')->count('gestor'),
            'total_empresas'    => Comentario::distinct('empresa')->count('empresa'),
        ];

        $yaImportado = Comentario::count() > 0;

        return view('cargues.comentarios', compact('stats', 'yaImportado'));
    }

    /**
     * Importar archivo XLSX de comentarios (cargue inicial, una sola vez).
     */
    public function importarComentarios(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:20480', // Max 20MB
        ]);

        // Verificar si ya se hizo el cargue inicial (solo bloquear si hay registros reales)
        if (Comentario::count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ya se realizó el cargue inicial de comentarios. No se permite subir nuevamente.',
            ], 422);
        }

        try {
            $import = new ComentariosImport();
            Excel::import($import, $request->file('archivo'));

            $resultado = $import->getResultado();

            return response()->json([
                'success'    => true,
                'message'    => "Importación completada: {$resultado['nuevos']} comentarios importados, {$resultado['duplicados']} duplicados omitidos, {$resultado['errores']} con errores.",
                'nuevos'     => $resultado['nuevos'],
                'duplicados' => $resultado['duplicados'],
                'errores'    => $resultado['errores'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Listar comentarios con filtros y relación con terceros.
     */
    public function listarComentarios(Request $request): JsonResponse
    {
        $query = Comentario::query();

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('cedula', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%")
                  ->orWhere('gestor', 'like', "%{$buscar}%")
                  ->orWhere('comentario', 'like', "%{$buscar}%")
                  ->orWhere('empresa', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('canal')) {
            $query->where('canal', $request->input('canal'));
        }

        if ($request->filled('efecto')) {
            $query->where('efecto_gestion', $request->input('efecto'));
        }

        if ($request->filled('gestor')) {
            $query->where('gestor', $request->input('gestor'));
        }

        $registros = $query->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(50);

        return response()->json($registros);
    }
}
