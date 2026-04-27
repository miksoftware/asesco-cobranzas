<?php

namespace App\Services;

use App\Models\EpsSystem;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EpsConsultaService
{
    /**
     * Consulta una cédula en todos los sistemas EPS activos de forma concurrente.
     *
     * @return array<string, array{system: EpsSystem, success: bool, data: array|null, error: string|null}>
     */
    public function consultarCedula(string $cedula): array
    {
        $systems = EpsSystem::active()->get();

        if ($systems->isEmpty()) {
            return [];
        }

        // Peticiones concurrentes
        $responses = Http::pool(function (Pool $pool) use ($systems, $cedula) {
            foreach ($systems as $system) {
                $pool->as($system->slug)
                    ->withToken($system->api_token)
                    ->accept('application/json')
                    ->timeout($system->timeout)
                    ->connectTimeout(5)
                    ->get($system->buildUrl($cedula));
            }
        });

        // Procesar respuestas
        $results = [];

        foreach ($systems as $system) {
            $slug = $system->slug;

            try {
                $response = $responses[$slug];

                if ($response instanceof \Throwable) {
                    $results[$slug] = [
                        'system'  => $system,
                        'success' => false,
                        'data'    => null,
                        'error'   => 'Error de conexión: ' . $response->getMessage(),
                    ];
                    continue;
                }

                if ($response->successful()) {
                    $body     = $response->json();
                    $rawData  = $body['data'] ?? null;

                    // Las APIs retornan un array ordenado más reciente → más antiguo.
                    // Para la vista del agregador tomamos el registro más reciente (índice 0).
                    $data = (is_array($rawData) && array_is_list($rawData))
                        ? ($rawData[0] ?? null)
                        : $rawData;

                    $results[$slug] = [
                        'system'  => $system,
                        'success' => $body['success'] ?? false,
                        'data'    => $data,
                        'error'   => null,
                    ];
                } elseif ($response->status() === 404) {
                    $results[$slug] = [
                        'system'  => $system,
                        'success' => false,
                        'data'    => null,
                        'error'   => null, // No encontrado, no es error
                    ];
                } else {
                    $results[$slug] = [
                        'system'  => $system,
                        'success' => false,
                        'data'    => null,
                        'error'   => "HTTP {$response->status()}",
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning("EPS consulta error [{$slug}]", ['error' => $e->getMessage()]);
                $results[$slug] = [
                    'system'  => $system,
                    'success' => false,
                    'data'    => null,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
