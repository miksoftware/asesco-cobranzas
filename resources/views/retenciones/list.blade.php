@extends('layouts.app')

@section('title', 'Listado de Retenciones')
@section('page-title', 'Listado de Retenciones')

@section('content')
<div class="space-y-4" x-data="listData()">

    <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-lg border border-gray-200 shadow-sm gap-4">
        <h3 class="text-sm font-bold text-gray-700">Retenciones Registradas</h3>
        
        <form @submit.prevent class="flex-1 max-w-md flex items-center gap-2">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="search" placeholder="Buscar por nombre, cédula o radicación..." 
                       @input.debounce.500ms="fetchTable()"
                       class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-asesco-orange focus:ring-1 focus:ring-asesco-orange text-xs transition-colors">
            </div>
            <button x-show="search.length > 0" @click="clearSearch()" type="button" class="text-gray-500 hover:text-red-500 transition-colors bg-gray-100 hover:bg-gray-200 p-1.5 rounded" title="Limpiar búsqueda" style="display: none;">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </form>

        <a href="{{ route('retenciones.index') }}" class="bg-asesco-orange hover:bg-asesco-coral text-white text-[11px] px-4 py-1.5 rounded shadow-sm font-bold transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva Retención
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden relative min-h-[200px]">
        <!-- Loader -->
        <div x-show="loading" style="display: none;" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center transition-opacity duration-200">
            <svg class="animate-spin h-6 w-6 text-asesco-orange" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div id="table-container" @click="handlePaginationClick($event)">
            @include('retenciones.partials.list_table')
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('listData', () => ({
            search: '{{ request('search') }}',
            loading: false,

            async fetchTable(url = null) {
                this.loading = true;
                try {
                    let fetchUrl = url;
                    if (!fetchUrl) {
                        const params = new URLSearchParams();
                        if (this.search) params.append('search', this.search);
                        fetchUrl = `{{ route('retenciones.list') }}?${params.toString()}`;
                    }

                    const response = await fetch(fetchUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const html = await response.text();
                        document.getElementById('table-container').innerHTML = html;
                        // Actualizar la URL del navegador sin recargar para que se pueda compartir el link
                        window.history.pushState(null, '', fetchUrl);
                    }
                } catch (error) {
                    console.error('Error fetching table data:', error);
                } finally {
                    this.loading = false;
                }
            },

            clearSearch() {
                this.search = '';
                this.fetchTable();
            },

            handlePaginationClick(e) {
                const link = e.target.closest('a[href]');
                // Asegurarse de que el link de paginación que se clickea no sea un link externo o de acción
                if (link && link.href && link.href.includes('retenciones/listado') && !link.href.includes('retenciones/')) {
                    e.preventDefault();
                    this.fetchTable(link.href);
                }
                
                // Excepción para los links de Paginación de Tailwind
                if (link && link.href && link.href.includes('page=')) {
                    e.preventDefault();
                    this.fetchTable(link.href);
                }
            }
        }));
    });
</script>
@endsection
