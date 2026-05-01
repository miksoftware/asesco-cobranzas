<?php

namespace App\Imports;

use App\Models\Tercero;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TercerosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected int $nuevos = 0;
    protected int $duplicados = 0;
    protected int $errores = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $referencia    = $this->clean($row['referencia_unica_cc_tt'] ?? $row['referencia_unica'] ?? $row['referencia'] ?? null);
            $cedula        = $this->clean($row['cedula_tercero'] ?? $row['cedula'] ?? null);
            $nombre        = $this->cleanText($row['nombre_tercero'] ?? $row['nombre'] ?? null);
            $calidad       = $this->normalizeCalidad($row['calidad_del_tercero'] ?? $row['calidad'] ?? null);
            $empresa       = $this->cleanText($row['empresa'] ?? null);
            $dato          = $this->clean($row['dato'] ?? null);
            $tipoDato      = $this->normalizeTipoDato($row['tipo_de_dato'] ?? $row['tipo_dato'] ?? null);

            // Validar campos obligatorios
            if (!$referencia || !$cedula || !$nombre || !$calidad || !$empresa || !$dato || !$tipoDato) {
                $this->errores++;
                continue;
            }

            // Insertar solo si no existe un registro idéntico
            $exists = Tercero::where('referencia', $referencia)
                ->where('cedula_tercero', $cedula)
                ->where('empresa', $empresa)
                ->where('dato', $dato)
                ->where('tipo_dato', $tipoDato)
                ->exists();

            if ($exists) {
                $this->duplicados++;
                continue;
            }

            Tercero::create([
                'referencia'      => $referencia,
                'cedula_tercero'  => $cedula,
                'nombre_tercero'  => $nombre,
                'calidad'         => $calidad,
                'empresa'         => $empresa,
                'dato'            => $dato,
                'tipo_dato'       => $tipoDato,
                'user_id'         => Auth::id(),
            ]);

            $this->nuevos++;
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

    private function clean(?string $value): ?string
    {
        if ($value === null) return null;
        $value = trim((string) $value);
        return $value !== '' ? $value : null;
    }

    private function cleanText(?string $value): ?string
    {
        if ($value === null) return null;
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        return $value !== '' ? mb_strtoupper($value) : null;
    }

    private function normalizeCalidad(?string $value): ?string
    {
        if ($value === null) return null;
        $value = mb_strtoupper(trim((string) $value));
        return in_array($value, ['TT', 'CD']) ? $value : null;
    }

    private function normalizeTipoDato(?string $value): ?string
    {
        if ($value === null) return null;
        $value = mb_strtolower(trim((string) $value));

        // Normalizar variantes comunes
        $map = [
            'celular'  => 'celular',
            'cel'      => 'celular',
            'movil'    => 'celular',
            'móvil'    => 'celular',
            'fijo'     => 'fijo',
            'telefono' => 'fijo',
            'teléfono' => 'fijo',
            'correo'   => 'correo',
            'email'    => 'correo',
            'e-mail'   => 'correo',
        ];

        return $map[$value] ?? $value;
    }
}
