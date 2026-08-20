@extends('layouts.app')

@section('title', $empresa->nombre)
@section('page-title', $empresa->nombre)

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('empresas.index') }}" class="hover:text-[#E8611A] transition-colors">Empresas</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">{{ $empresa->nombre }}</span>
        </div>
        <a href="{{ route('empresas.edit', $empresa) }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-5 py-2 rounded-lg hover:opacity-90 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar
        </a>
    </div>

    {{-- Header card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#E8611A]/20 to-[#C94477]/10 flex items-center justify-center">
                <svg class="w-7 h-7 text-[#E8611A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $empresa->nombre }}</h2>
                <span class="inline-flex items-center gap-1.5 text-sm mt-1 {{ $empresa->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    <span class="w-2 h-2 rounded-full {{ $empresa->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                    {{ $empresa->is_active ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Sección Tarifas --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-[#E8611A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-bold text-gray-800">Tarifas y Honorarios</h3>
        </div>
        @if($empresa->tarifas->isEmpty())
            <p class="text-sm text-gray-400 italic">No hay tarifas configuradas.</p>
        @else
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">TRAMO</th>
                        <th class="text-center px-4 py-3 font-semibold text-[#E8611A] bg-orange-50">VIGENTE</th>
                        <th class="text-center px-4 py-3 font-semibold text-[#C94477] bg-pink-50">CASTIGO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->tarifas as $tarifa)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-medium text-gray-700">{{ $tarifa->nombre_tramo }}</td>
                        <td class="px-4 py-2.5 text-center bg-orange-50/50 text-[#E8611A] font-semibold">{{ $tarifa->porcentaje_vigente }}%</td>
                        <td class="px-4 py-2.5 text-center bg-pink-50/50 text-[#C94477] font-semibold">{{ $tarifa->porcentaje_castigada }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Sección Canales --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h3 class="font-bold text-gray-800">Canales de Recaudo</h3>
        </div>
        @if($empresa->canales->isEmpty())
            <p class="text-sm text-gray-400 italic">No hay canales configurados.</p>
        @else
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">NOMBRE DEL CANAL</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">N° CANAL</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">MEDIO DE PAGO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->canales as $canal)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-medium text-gray-700">{{ $canal->nombre_canal }}</td>
                        <td class="px-4 py-2.5 text-gray-600 font-mono text-xs">{{ $canal->numero_canal ?: '—' }}</td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $canal->medio_pago }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Sección Lineamientos --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-bold text-gray-800">Lineamientos de Negociación</h3>
        </div>
        @if($empresa->lineamientos->isEmpty())
            <p class="text-sm text-gray-400 italic">No hay lineamientos configurados.</p>
        @else
            <div class="space-y-4">
            @foreach($empresa->lineamientos as $lin)
                <div class="border-2 rounded-xl p-4 transition-all {{ $lin->is_active ? 'border-[#E8611A] bg-orange-50/20' : 'border-gray-200' }}">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 flex-wrap">
                            @if($lin->tipo === 'porcentaje')
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-orange-100 text-[#E8611A]">Por Porcentaje</span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-pink-100 text-[#C94477]">Por Tramo</span>
                            @endif
                            <span class="font-semibold text-gray-800 text-sm">{{ $lin->concepto }}</span>
                            @if($lin->is_active)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Activo
                                </span>
                            @else
                                <button onclick="activarLineamiento({{ $empresa->id }}, {{ $lin->id }}, this)"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 hover:bg-[#E8611A] hover:text-white px-2.5 py-1 rounded-full transition-all">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Activar
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($lin->tipo === 'porcentaje' && $lin->porcentaje)
                    <div class="grid grid-cols-2 gap-4 max-w-xs">
                        <div class="text-center bg-orange-50 rounded-lg py-2">
                            <p class="text-xl font-bold text-[#E8611A]">{{ $lin->porcentaje->porcentaje_vigente }}%</p>
                            <p class="text-xs text-gray-500">Vigente</p>
                        </div>
                        <div class="text-center bg-pink-50 rounded-lg py-2">
                            <p class="text-xl font-bold text-[#C94477]">{{ $lin->porcentaje->porcentaje_castigado }}%</p>
                            <p class="text-xs text-gray-500">Castigado</p>
                        </div>
                    </div>
                    @elseif($lin->tipo === 'tramo')
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        {{-- Vigente --}}
                        <div>
                            <h4 class="text-xs font-bold text-[#E8611A] uppercase tracking-wide mb-2">Tramos Vigente</h4>
                            <div class="border border-orange-100 rounded-lg overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead class="bg-orange-50">
                                        <tr><th class="text-left px-3 py-2 font-semibold text-[#E8611A]">Tramo</th><th class="text-center px-3 py-2 font-semibold text-[#E8611A]">%</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lin->tramos->where('tipo_cartera','vigente') as $tramo)
                                        <tr class="border-t border-gray-100"><td class="px-3 py-1.5 text-gray-700">{{ $tramo->nombre_tramo }}</td><td class="px-3 py-1.5 text-center font-semibold text-[#E8611A]">{{ $tramo->porcentaje }}%</td></tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- Castigado --}}
                        <div>
                            <h4 class="text-xs font-bold text-[#C94477] uppercase tracking-wide mb-2">Tramos Castigado</h4>
                            <div class="border border-pink-100 rounded-lg overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead class="bg-pink-50">
                                        <tr><th class="text-left px-3 py-2 font-semibold text-[#C94477]">Tramo</th><th class="text-center px-3 py-2 font-semibold text-[#C94477]">%</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lin->tramos->where('tipo_cartera','castigado') as $tramo)
                                        <tr class="border-t border-gray-100"><td class="px-3 py-1.5 text-gray-700">{{ $tramo->nombre_tramo }}</td><td class="px-3 py-1.5 text-center font-semibold text-[#C94477]">{{ $tramo->porcentaje }}%</td></tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function activarLineamiento(empresaId, lineamientoId, btn) {
    btn.disabled = true;
    btn.textContent = '...';
    try {
        const res = await fetch(`/empresas/${empresaId}/lineamientos/${lineamientoId}/activar`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error('Error al activar');
        // Reload para reflejar cambios
        window.location.reload();
    } catch(e) {
        Swal.fire('Error', e.message, 'error');
        btn.disabled = false;
        btn.textContent = 'Activar';
    }
}
</script>
@endpush
