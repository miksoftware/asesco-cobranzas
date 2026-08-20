@extends('layouts.app')

@section('title', 'Empresas')
@section('page-title', 'Empresas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Empresas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gestiona las empresas y sus configuraciones de tarifas, canales y lineamientos.</p>
    </div>
    <a href="{{ route('empresas.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition-opacity shadow-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Empresa
    </a>
</div>

@if($empresas->isEmpty())
<div class="bg-white rounded-xl border border-gray-200 p-16 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-700 mb-1">No hay empresas registradas</h3>
    <p class="text-sm text-gray-400 mb-5">Crea tu primera empresa para comenzar.</p>
    <a href="{{ route('empresas.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-[#E8611A] to-[#C94477] text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition-opacity">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Crear Empresa
    </a>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($empresas as $empresa)
    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#E8611A]/20 to-[#C94477]/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-[#E8611A]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-[15px] leading-tight">{{ $empresa->nombre }}</h3>
                    <span class="inline-flex items-center gap-1 text-xs mt-0.5 {{ $empresa->is_active ? 'text-green-600' : 'text-gray-400' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $empresa->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        {{ $empresa->is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="text-center bg-gray-50 rounded-lg py-2">
                <p class="text-lg font-bold text-gray-800">{{ $empresa->tarifas->count() }}</p>
                <p class="text-[11px] text-gray-400 leading-tight">Tramos</p>
            </div>
            <div class="text-center bg-gray-50 rounded-lg py-2">
                <p class="text-lg font-bold text-gray-800">{{ $empresa->canales->count() }}</p>
                <p class="text-[11px] text-gray-400 leading-tight">Canales</p>
            </div>
            <div class="text-center bg-gray-50 rounded-lg py-2">
                <p class="text-lg font-bold text-gray-800">{{ $empresa->lineamientos->count() }}</p>
                <p class="text-[11px] text-gray-400 leading-tight">Lineamientos</p>
            </div>
        </div>

        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
            <a href="{{ route('empresas.edit', $empresa) }}"
               class="flex-1 text-center text-sm font-medium text-white bg-gradient-to-r from-[#E8611A] to-[#C94477] py-2 rounded-lg hover:opacity-90 transition-opacity">
                Editar
            </a>
            <a href="{{ route('empresas.show', $empresa) }}"
               class="flex-1 text-center text-sm font-medium text-gray-600 bg-gray-100 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                Ver detalle
            </a>
            <button onclick="confirmarEliminar({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}')"
                    class="w-9 h-9 flex items-center justify-center text-red-400 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

<form id="form-eliminar" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmarEliminar(id, nombre) {
    Swal.fire({
        title: '¿Eliminar empresa?',
        html: `Se eliminará <strong>${nombre}</strong> y toda su configuración.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#E8611A',
        cancelButtonColor: '#6b7280',
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.getElementById('form-eliminar');
            form.action = `/empresas/${id}`;
            form.submit();
        }
    });
}
</script>
@endpush
