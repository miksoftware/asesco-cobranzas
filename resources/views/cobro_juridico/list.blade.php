@extends('layouts.app')

@section('title', 'Listado de Cobros Jurídicos')
@section('page-title', 'Listado de Cobros Jurídicos')

@section('content')
<div class="space-y-4" x-data="listData()">

    <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-lg border border-gray-200 shadow-sm gap-4">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-asesco-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-16.5 0c-.99.203-1.99.377-3 .52m0 0l3 9m-3-9l3 9m0 0h12m0 0l3-9m-3 9l3-9m-15 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Cobros Jurídicos Registrados</h3>
        </div>
        
        <form @submit.prevent class="flex-1 max-w-md flex items-center gap-2">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="search" placeholder="Buscar por radicado, cédula, nombre o juzgado..." 
                       @input.debounce.500ms="fetchTable()"
                       class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-asesco-orange focus:ring-1 focus:ring-asesco-orange text-xs transition-colors">
            </div>
            <button x-show="search.length > 0" @click="clearSearch()" type="button" class="text-gray-500 hover:text-red-500 transition-colors bg-gray-100 hover:bg-gray-200 p-1.5 rounded cursor-pointer" title="Limpiar búsqueda" style="display: none;">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </form>
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
            @include('cobro_juridico.partials.list_table')
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
                        fetchUrl = `{{ route('cobros-juridicos.list') }}?${params.toString()}`;
                    }

                    const response = await fetch(fetchUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const html = await response.text();
                        document.getElementById('table-container').innerHTML = html;
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
                if (link && link.href && link.href.includes('cobros-juridicos/listado') && !link.href.includes('cobros-juridicos/')) {
                    e.preventDefault();
                    this.fetchTable(link.href);
                }
                if (link && link.href && link.href.includes('page=')) {
                    e.preventDefault();
                    this.fetchTable(link.href);
                }
            }
        }));
    });
</script>
@endsection
