<div class="overflow-x-auto">
    <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-xs uppercase">
            <tr>
                <th class="px-4 py-3">No. Radicación</th>
                <th class="px-4 py-3">Fecha Radicación</th>
                <th class="px-4 py-3">Cédula TT</th>
                <th class="px-4 py-3">Nombre de TT</th>
                <th class="px-4 py-3">Portafolio</th>
                <th class="px-4 py-3">Valor Retención</th>
                <th class="px-4 py-3">Recaudo Retención</th>
                <th class="px-4 py-3">Saldo Pendiente</th>
                <th class="px-4 py-3">Recaudo Mes Actual</th>
                <th class="px-4 py-3">Efecto Gestión</th>
                <th class="px-4 py-3 text-center">Estado</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse($retenciones as $retencion)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $retencion->no_radicacion ?? 'N/A' }}</td>
                <td class="px-4 py-3">{{ $retencion->fecha_radicacion ?? 'N/A' }}</td>
                <td class="px-4 py-3">{{ $retencion->cedula_tt ?? 'N/A' }}</td>
                <td class="px-4 py-3">{{ $retencion->nombre_tt ?? 'N/A' }}</td>
                <td class="px-4 py-3">{{ $retencion->portafolio_empresa ?? 'N/A' }}</td>
                <td class="px-4 py-3">${{ number_format($retencion->valor_retencion_total, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-green-600 font-medium">${{ number_format($retencion->recaudo_total, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-red-600 font-medium">${{ number_format($retencion->saldo_pendiente, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-green-600 font-medium">${{ number_format($retencion->recaudo_mes_actual, 0, ',', '.') }}</td>
                <td class="px-4 py-3">{{ $retencion->efecto_gestion_retencion ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-center">
                    @if($retencion->estado_retencion)
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700">
                            {{ $retencion->estado_retencion }}
                        </span>
                    @else
                        <span class="text-gray-400 italic text-xs">Sin estado</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('retenciones.show', $retencion->id) }}" class="inline-flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 p-1.5 rounded transition-colors" title="Ver / Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">
                    No se han registrado retenciones aún.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3 border-t border-gray-200">
    {{ $retenciones->links() }}
</div>
