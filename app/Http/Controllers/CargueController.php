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
            'archivo'              => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
            'default_tipo_cartera' => 'nullable|string|in:Vigente,Castigada',
        ]);

        try {
            $import = new TercerosImport($request->input('default_tipo_cartera'));
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
     * Validar contenido y estructura del archivo Excel antes de procesar.
     */
    public function validarTerceros(Request $request): JsonResponse
    {
        $request->validate([
            'archivo'              => 'required|file|mimes:xlsx,xls|max:10240',
            'default_tipo_cartera' => 'nullable|string|in:Vigente,Castigada',
        ]);

        $defaultCartera = $request->input('default_tipo_cartera');
        $file = $request->file('archivo');

        try {
            $array = Excel::toArray(new \stdClass(), $file);
            if (empty($array) || empty($array[0])) {
                return response()->json([
                    'valid'   => false,
                    'message' => 'El archivo Excel se encuentra vacío.',
                    'errores' => [['fila' => 1, 'referencia' => '-', 'cedula' => '-', 'campo' => 'Archivo', 'error' => 'El archivo está vacío.']],
                ], 422);
            }

            $rows = $array[0];
            $headerRow = array_map(function ($val) {
                return mb_strtolower(trim((string) $val));
            }, $rows[0] ?? []);

            $headersMap = [];
            foreach ($headerRow as $idx => $h) {
                $hClean = preg_replace('/[^a-z0-9_]/', '', str_replace([' ', '-'], '_', $h));
                $headersMap[$hClean] = $idx;
            }

            $findCol = function (array $names) use ($headersMap) {
                foreach ($names as $n) {
                    if (isset($headersMap[$n])) return $headersMap[$n];
                }
                return null;
            };

            $colRef      = $findCol(['referencia_unica_cc_tt', 'referencia_unica', 'referencia']);
            $colCedula   = $findCol(['cedula_tercero', 'cedula']);
            $colNombre   = $findCol(['nombre_tercero', 'nombre']);
            $colCalidad  = $findCol(['calidad_del_tercero', 'calidad']);
            $colEmpresa  = $findCol(['empresa']);
            $colDato     = $findCol(['dato']);
            $colTipoDato = $findCol(['tipo_de_dato', 'tipo_dato']);
            $colCartera  = $findCol(['tipo_cartera', 'tipo_de_cartera', 'cartera']);

            $missingHeaders = [];
            if ($colRef === null) $missingHeaders[] = 'REFERENCIA_UNICA_CC_TT';
            if ($colCedula === null) $missingHeaders[] = 'CEDULA_TERCERO';
            if ($colNombre === null) $missingHeaders[] = 'NOMBRE_TERCERO';
            if ($colCalidad === null) $missingHeaders[] = 'CALIDAD_DEL_TERCERO';
            if ($colEmpresa === null) $missingHeaders[] = 'EMPRESA';
            if ($colDato === null) $missingHeaders[] = 'DATO';
            if ($colTipoDato === null) $missingHeaders[] = 'TIPO_DE_DATO';
            if ($colCartera === null) $missingHeaders[] = 'TIPO_CARTERA';

            if (!empty($missingHeaders)) {
                return response()->json([
                    'valid'   => false,
                    'message' => 'Faltan columnas requeridas en el archivo Excel: ' . implode(', ', $missingHeaders),
                    'errores' => [[
                        'fila'       => 1,
                        'referencia' => '-',
                        'cedula'     => '-',
                        'campo'      => 'Encabezados',
                        'error'      => 'Faltan las columnas obligatorias: ' . implode(', ', $missingHeaders)
                    ]],
                ]);
            }

            $importHelper = new TercerosImport();
            $errores = [];
            $totalFilas = 0;
            $statsVigente = 0;
            $statsCastigada = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $excelFila = $i + 1;

                $isEmptyRow = true;
                foreach ($row as $cell) {
                    if ($cell !== null && trim((string) $cell) !== '') {
                        $isEmptyRow = false;
                        break;
                    }
                }
                if ($isEmptyRow) continue;

                $totalFilas++;

                $valRef      = trim((string) ($row[$colRef] ?? ''));
                $valCedula   = trim((string) ($row[$colCedula] ?? ''));
                $valNombre   = trim((string) ($row[$colNombre] ?? ''));
                $valCalidad  = trim((string) ($row[$colCalidad] ?? ''));
                $valEmpresa  = trim((string) ($row[$colEmpresa] ?? ''));
                $valDato     = trim((string) ($row[$colDato] ?? ''));
                $valTipoDato = trim((string) ($row[$colTipoDato] ?? ''));
                $valCartera  = trim((string) ($row[$colCartera] ?? ''));

                if ($valRef === '') {
                    $errores[] = ['fila' => $excelFila, 'referencia' => '-', 'cedula' => $valCedula ?: '-', 'campo' => 'REFERENCIA_UNICA_CC_TT', 'error' => 'La referencia es obligatoria.'];
                }
                if ($valCedula === '') {
                    $errores[] = ['fila' => $excelFila, 'referencia' => $valRef ?: '-', 'cedula' => '-', 'campo' => 'CEDULA_TERCERO', 'error' => 'La cédula del tercero es obligatoria.'];
                }
                if ($valNombre === '') {
                    $errores[] = ['fila' => $excelFila, 'referencia' => $valRef ?: '-', 'cedula' => $valCedula ?: '-', 'campo' => 'NOMBRE_TERCERO', 'error' => 'El nombre del tercero es obligatorio.'];
                }

                $calidadNorm = mb_strtoupper($valCalidad);
                if (!in_array($calidadNorm, ['TT', 'CD', 'TITULAR', 'CODEUDOR'])) {
                    $errores[] = ['fila' => $excelFila, 'referencia' => $valRef ?: '-', 'cedula' => $valCedula ?: '-', 'campo' => 'CALIDAD_DEL_TERCERO', 'error' => "Calidad '{$valCalidad}' no válida. Debe ser 'TT', 'CD', 'TITULAR' o 'CODEUDOR'."];
                }

                if ($valEmpresa === '') {
                    $errores[] = ['fila' => $excelFila, 'referencia' => $valRef ?: '-', 'cedula' => $valCedula ?: '-', 'campo' => 'EMPRESA', 'error' => 'La empresa es obligatoria.'];
                }
                if ($valDato === '') {
                    $errores[] = ['fila' => $excelFila, 'referencia' => $valRef ?: '-', 'cedula' => $valCedula ?: '-', 'campo' => 'DATO', 'error' => 'El dato (teléfono/correo) es obligatorio.'];
                }

                $tipoDatoNorm = mb_strtolower($valTipoDato);
                $validTiposDato = ['celular', 'cel', 'movil', 'móvil', 'fijo', 'telefono', 'teléfono', 'correo', 'email', 'e-mail'];
                if (!in_array($tipoDatoNorm, $validTiposDato)) {
                    $errores[] = ['fila' => $excelFila, 'referencia' => $valRef ?: '-', 'cedula' => $valCedula ?: '-', 'campo' => 'TIPO_DE_DATO', 'error' => "Tipo de dato '{$valTipoDato}' inválido. Debe ser 'celular', 'fijo' o 'correo'."];
                }

                $carteraNorm = $importHelper->normalizeTipoCartera($valCartera);
                if (!$carteraNorm) {
                    $errores[] = [
                        'fila'       => $excelFila,
                        'referencia' => $valRef ?: '-',
                        'cedula'     => $valCedula ?: '-',
                        'campo'      => 'TIPO_CARTERA',
                        'error'      => $valCartera !== ''
                            ? "Tipo de cartera '{$valCartera}' no válido. Debe ser 'Vigente' o 'Castigada'."
                            : "El campo TIPO_CARTERA es obligatorio. Debe ser 'Vigente' o 'Castigada'."
                    ];
                } else {
                    if ($carteraNorm === 'Vigente') $statsVigente++;
                    if ($carteraNorm === 'Castigada') $statsCastigada++;
                }
            }

            $isValid = count($errores) === 0;

            return response()->json([
                'valid'       => $isValid,
                'total_filas' => $totalFilas,
                'validas'     => $isValid ? $totalFilas : ($totalFilas - count(array_unique(array_column($errores, 'fila')))),
                'stats'       => [
                    'vigente'   => $statsVigente,
                    'castigada' => $statsCastigada,
                ],
                'errores'     => array_slice($errores, 0, 100),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'valid'   => false,
                'message' => 'Error al leer el archivo Excel: ' . $e->getMessage(),
                'errores' => [['fila' => 1, 'referencia' => '-', 'cedula' => '-', 'campo' => 'Archivo', 'error' => $e->getMessage()]],
            ], 422);
        }
    }

    /**
     * Descargar plantilla Excel XLSX para asignación de terceros.
     */
    public function descargarPlantilla()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Terceros');

        $headers = [
            'REFERENCIA_UNICA_CC_TT',
            'CEDULA_TERCERO',
            'NOMBRE_TERCERO',
            'CALIDAD_DEL_TERCERO',
            'EMPRESA',
            'DATO',
            'TIPO_DE_DATO',
            'TIPO_CARTERA',
        ];

        $sheet->fromArray([$headers], null, 'A1');

        $sampleData = [
            ['1003714361', '1003714361', 'ROJAS PEREZ ANA ISABEL', 'TITULAR', 'COOMULTRASAN', '3008924855', 'celular', 'Vigente'],
            ['1003714361', '1003714361', 'ROJAS PEREZ ANA ISABEL', 'TITULAR', 'COOMULTRASAN', 'no_tiene@pendiente.com', 'correo', 'Castigada'],
            ['9876543210', '1122334455', 'GOMEZ MARTINEZ CARLOS', 'CODEUDOR', 'COOMULTRASAN', '6076543210', 'fijo', 'Vigente'],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8611A']],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Plantilla_Asignacion_Terceros.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
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

        if ($request->filled('tipo_cartera')) {
            $query->where('tipo_cartera', $request->input('tipo_cartera'));
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
        $gestores  = Comentario::distinct()->orderBy('gestor')->pluck('gestor')->filter()->values();
        $empresas  = Comentario::distinct()->orderBy('empresa')->pluck('empresa')->filter()->values();

        return view('cargues.comentarios', compact('stats', 'yaImportado', 'gestores', 'empresas'));
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
     * Borrar todos los registros del cargue de comentarios (solo superadmin).
     */
    public function borrarCargueComentarios(): JsonResponse
    {
        $total = Comentario::count();
        Comentario::truncate();

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$total} comentarios. El cargue inicial puede realizarse nuevamente.",
        ]);
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

        if ($request->filled('empresa')) {
            $query->where('empresa', $request->input('empresa'));
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }

        $registros = $query->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(50);

        return response()->json($registros);
    }
}
