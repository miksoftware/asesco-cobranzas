<div class="w-full overflow-hidden">
    <table class="w-full text-left text-[11px] leading-tight">
        <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-600 font-bold text-[10px] uppercase tracking-wider">
            <tr>
                <th class="px-2 py-2.5 whitespace-nowrap">No. Radicado</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Cédula TT</th>
                <th class="px-2 py-2.5">Demandado 1</th>
                <th class="px-2 py-2.5">Demandado 2</th>
                <th class="px-2 py-2.5">Juzgado</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Fecha Etapa</th>
                <th class="px-2 py-2.5">Etapa Procesal</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Fecha Actividad</th>
                <th class="px-2 py-2.5">Actividad</th>
                <th class="px-2 py-2.5 whitespace-nowrap">Estado Proceso</th>
                <th class="px-2 py-2.5">Anotación Abogado</th>
                <th class="px-2 py-2.5 text-center whitespace-nowrap">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse($cobros as $cobro)
            <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-2 py-2 font-bold text-gray-900 whitespace-nowrap">{{ $cobro->no_radicado ?? 'N/A' }}</td>
                <td class="px-2 py-2 font-mono text-gray-600 whitespace-nowrap">{{ $cobro->cedula ?? 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[140px] font-medium text-gray-800">{{ $cobro->demandado_1 ?? 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[140px] font-medium text-gray-800">{{ $cobro->demandado_2 ?? 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[140px] text-gray-600" title="{{ $cobro->juzgado_conocimiento }}">{{ $cobro->juzgado_conocimiento ?? 'N/A' }}</td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-600">{{ $cobro->fecha_etapa ? substr($cobro->fecha_etapa, 0, 10) : 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[120px] text-gray-600">{{ $cobro->etapa_procesal ?? 'N/A' }}</td>
                <td class="px-2 py-2 whitespace-nowrap text-gray-600">{{ $cobro->fecha_actividad ? substr($cobro->fecha_actividad, 0, 10) : 'N/A' }}</td>
                <td class="px-2 py-2 break-words max-w-[140px] text-gray-600">{{ $cobro->actividad ?? 'N/A' }}</td>
                <td class="px-2 py-2 whitespace-nowrap">
                    @if($cobro->estado_proceso)
                        <span class="px-2 py-0.5 text-[9.5px] font-bold rounded-full bg-orange-50 text-orange-700 ring-1 ring-orange-200">
                            {{ $cobro->estado_proceso }}
                        </span>
                    @else
                        <span class="text-gray-400 italic text-[10px]">Sin estado</span>
                    @endif
                </td>
                <td class="px-2 py-2 max-w-[180px] text-gray-600">
                    <div class="truncate" title="{{ $cobro->anotacion_abogado }}">{{ $cobro->anotacion_abogado ?? 'N/A' }}</div>
                </td>
                <td class="px-2 py-2 text-center whitespace-nowrap">
                    <a href="{{ route('cobros-juridicos.show', $cobro->id) }}" class="inline-flex items-center justify-center bg-orange-50 hover:bg-orange-100 text-asesco-orange border border-orange-200 p-1 rounded transition-colors" title="Ver / Editar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="px-4 py-8 text-center text-gray-500 italic">
                    No se han registrado cobros jurídicos aún.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3 border-t border-gray-200">
    {{ $cobros->links() }}
</div>
