<?php

namespace App\Imports;

use App\Models\Comentario;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class ComentariosImport implements ToCollection, WithHeadingRow, WithChunkReading, WithEvents
{
    protected int $nuevos = 0;
    protected int $duplicados = 0;
    protected int $errores = 0;
    protected bool $headingsLogged = false;

    /**
     * Cache de valores de fórmulas pre-leídos del Excel.
     * Clave: "row_col" => valor cacheado
     */
    protected array $formulaCache = [];

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Pre-leer valores cacheados de celdas con fórmulas
                for ($row = 2; $row <= $highestRow; $row++) {
                    foreach (range('A', 'K') as $col) {
                        $cell = $sheet->getCell("{$col}{$row}");
                        if ($cell->getDataType() === 'f') {
                            // Es una fórmula: leer el valor cacheado (oldCalculatedValue)
                            $cached = $cell->getOldCalculatedValue();
                            if ($cached !== null) {
                                $this->formulaCache["{$row}_{$col}"] = $cached;
                            }
                        }
                    }
                }

                Log::info('ComentariosImport - Formula cache:', $this->formulaCache);
            },
        ];
    }

    public function collection(Collection $rows): void
    {
        $rowIndex = 1; // Starts at 1 because heading row is 0

        foreach ($rows as $row) {
            $rowIndex++;
            try {
                if (!$this->headingsLogged) {
                    Log::info('ComentariosImport - Keys:', $row->keys()->toArray());
                    Log::info('ComentariosImport - Row:', $row->toArray());
                    $this->headingsLogged = true;
                }

                // Saltar filas vacías
                $valores = $row->filter(fn($v) => $v !== null && trim((string) $v) !== '');
                if ($valores->isEmpty()) {
                    continue;
                }

                // Mapear a claves normalizadas
                $mapped = $this->mapRow($row);

                $fecha          = $this->parseFecha($mapped['fecha'] ?? null);
                $hora           = $this->parseHora($mapped['hora'] ?? null);
                $gestor         = $this->cleanText($mapped['gestor'] ?? null);
                $comentario     = $this->cleanComentario($mapped['comentario'] ?? null);
                $canal          = $this->cleanText($mapped['canal'] ?? null);
                $tipoContacto   = $this->cleanText($mapped['tipo_de_contacto'] ?? null);
                $accionCobro    = $this->cleanText($mapped['accion_de_cobro'] ?? null);
                $cedula         = $this->cleanCedula($mapped['cedula'] ?? null);
                $nombre         = $this->cleanText($mapped['nombre'] ?? null);
                $empresa        = $this->cleanText($mapped['empresa'] ?? null);

                // Efecto de gestión: usar cache de fórmula si existe, sino el valor directo
                $efectoRaw = $mapped['efecto_de_gestion'] ?? null;
                // Si el valor raw es una fórmula o #N/A, buscar en cache
                if ($efectoRaw === null || str_starts_with((string) $efectoRaw, '=') || str_contains((string) $efectoRaw, '#N/A') || str_contains((string) $efectoRaw, 'VLOOKUP')) {
                    $cachedVal = $this->formulaCache["{$rowIndex}_G"] ?? null;
                    if ($cachedVal !== null) {
                        $efectoRaw = $cachedVal;
                    }
                }
                $efectoGestion = $this->normalizeEfecto($efectoRaw);

                // Validar campos obligatorios
                if (!$cedula) continue;
                if (!$comentario && !$gestor) continue;

                // Verificar duplicado
                $query = Comentario::where('cedula', $cedula)->where('gestor', $gestor);
                if ($fecha) {
                    $query->where('fecha', $fecha);
                }
                if ($comentario) {
                    $query->whereRaw('SUBSTR(comentario, 1, 100) = ?', [mb_substr($comentario, 0, 100)]);
                }

                if ($query->exists()) {
                    $this->duplicados++;
                    continue;
                }

                Comentario::create([
                    'fecha'          => $fecha ?? now()->format('Y-m-d'),
                    'hora'           => $hora,
                    'gestor'         => $gestor ?? '—',
                    'comentario'     => $comentario ?? '—',
                    'canal'          => $canal,
                    'tipo_contacto'  => $tipoContacto,
                    'efecto_gestion' => $efectoGestion,
                    'accion_cobro'   => $accionCobro,
                    'cedula'         => $cedula,
                    'nombre'         => $nombre ?? '—',
                    'empresa'        => $empresa ?? '—',
                    'user_id'        => Auth::id(),
                ]);

                $this->nuevos++;
            } catch (\Throwable $e) {
                Log::warning('ComentariosImport - Error:', [
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                ]);
                $this->errores++;
            }
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getResultado(): array
    {
        return [
            'nuevos'     => $this->nuevos,
            'duplicados' => $this->duplicados,
            'errores'    => $this->errores,
        ];
    }

    private function mapRow(Collection $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normKey = $this->normalizeKey((string) $key);
            $normalized[$normKey] = $value;
        }
        return $normalized;
    }

    private function normalizeKey(string $key): string
    {
        $key = mb_strtolower($key);
        $key = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $key
        );
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);
        return trim($key, '_');
    }

    private function normalizeEfecto($value): ?string
    {
        if ($value === null) return null;
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        if ($value === '' || $value === '0' || str_contains($value, '#N/A') || str_contains($value, '#REF')) return null;

        if (str_starts_with($value, '=') || str_contains(mb_strtoupper($value), 'VLOOKUP') || str_contains(mb_strtoupper($value), 'BUSCARV')) {
            return null;
        }

        $upper = mb_strtoupper($value);

        $map = [
            'EN GESTIÓN'        => 'EN GESTIÓN',
            'EN GESTION'        => 'EN GESTIÓN',
            'EN MENSAJE'        => 'EN MENSAJE',
            'INTENCIÓN DE PAGO' => 'INTENCIÓN DE PAGO',
            'INTENCION DE PAGO' => 'INTENCIÓN DE PAGO',
            'NO CONTESTA'       => 'NO CONTESTA',
            'PROMESA DE PAGO'   => 'PROMESA DE PAGO',
            'PROMESA ROTA'      => 'PROMESA ROTA',
            'RENUENTE'          => 'RENUENTE',
        ];

        return $map[$upper] ?? $upper;
    }

    private function parseFecha($value): ?string
    {
        if ($value === null) return null;

        if (is_numeric($value) && (float) $value > 1) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                )->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $value);
        if ($value === '') return null;

        $mesesEs = [
            'ene' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar', 'abr' => 'Apr',
            'may' => 'May', 'jun' => 'Jun', 'jul' => 'Jul', 'ago' => 'Aug',
            'sep' => 'Sep', 'oct' => 'Oct', 'nov' => 'Nov', 'dic' => 'Dec',
        ];
        $valueLower = mb_strtolower($value);
        foreach ($mesesEs as $es => $en) {
            $valueLower = str_replace($es, $en, $valueLower);
        }

        $formats = ['j-M-y', 'd-M-y', 'd/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $valueLower)->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($valueLower)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseHora($value): ?string
    {
        if ($value === null) return null;

        if (is_numeric($value) && (float) $value >= 0 && (float) $value < 1) {
            $totalMinutes = round((float) $value * 24 * 60);
            $hours = intdiv((int) $totalMinutes, 60);
            $minutes = (int) $totalMinutes % 60;
            $ampm = $hours >= 12 ? 'p. m.' : 'a. m.';
            $hours12 = $hours % 12 ?: 12;
            return sprintf('%d:%02d %s', $hours12, $minutes, $ampm);
        }

        $value = trim((string) $value);
        return $value !== '' ? $value : null;
    }

    private function cleanCedula($value): ?string
    {
        if ($value === null) return null;
        $value = preg_replace('/[^0-9]/', '', (string) $value);
        return $value !== '' ? $value : null;
    }

    private function cleanText($value): ?string
    {
        if ($value === null) return null;
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        return $value !== '' ? mb_strtoupper($value) : null;
    }

    private function cleanComentario($value): ?string
    {
        if ($value === null) return null;
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        return $value !== '' ? $value : null;
    }
}
