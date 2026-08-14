<div class="w-full overflow-hidden">
    <table class="w-full text-left text-[11px] leading-tight">
        <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-600 font-bold text-[10px] uppercase tracking-wider">
            <tr>
                <th class="px-2 py-2.5 whitespace-nowrap">No. Radicación</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Fecha Radicación</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Cédula TT</th>
                <th class="px-2 py-2.5">Nombre de TT</th>
                <th class="px-2 py-2.5">Portafolio</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Valor Retención</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Recaudo Retención</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Saldo Pendiente</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Recaudo Mes Actual</th>
                <th class="px-2 py-2.5">Efecto Gestión</th>
                <th class="px-2 py-2.5 text-center whitespace-nowrap">Estado</th>
                <th class="px-2 py-2.5 text-center whitespace-nowrap">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse($retenciones as $retencion)
            <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-2 py-2 font-medium text-gray-900 whitespace-nowrap">{{ $retencion->no_radicacion ?? 'N/A' }}</td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-500">{{ $retencion->fecha_radicacion ?? 'N/A' }}</td>
                <td class="px-2 py-2 whitespace-nowrap font-mono text-[10.5px] text-gray-600">{{ $retencion->cedula_tt ?? 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[140px] font-medium text-gray-800">{{ $retencion->nombre_tt ?? 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[140px] text-gray-600">{{ $retencion->portafolio_empresa ?? 'N/A' }}</td>
                <td class="px-2 py-2 whitespace-nowrap font-semibold text-gray-800">${{ number_format($retencion->valor_retencion_total, 0, ',', '.') }}</td>
                <td class="px-2 py-2 whitespace-nowrap font-semibold text-green-600">${{ number_format($retencion->recaudo_total, 0, ',', '.') }}</td>
                <td class="px-2 py-2 whitespace-nowrap font-semibold text-red-600">${{ number_format($retencion->saldo_pendiente, 0, ',', '.') }}</td>
                <td class="px-2 py-2 whitespace-nowrap font-semibold text-green-600">${{ number_format($retencion->recaudo_mes_actual, 0, ',', '.') }}</td>
                <td class="px-2 py-2 break-words max-w-[120px] text-gray-600">{{ $retencion->efecto_gestion_retencion ?? 'N/A' }}</td>
                <td class="px-2 py-2 text-center whitespace-nowrap">
                    @if($retencion->estado_retencion)
                        <span class="px-2 py-0.5 text-[9.5px] font-bold rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                            {{ $retencion->estado_retencion }}
                        </span>
                    @else
                        <span class="text-gray-400 italic text-[10px]">Sin estado</span>
                    @endif
                </td>
                <td class="px-2 py-2 text-center whitespace-nowrap">
                    <a href="{{ route('retenciones.show', $retencion->id) }}" class="inline-flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 p-1 rounded transition-colors" title="Ver / Editar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="px-4 py-8 text-center text-gray-500 italic">
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
