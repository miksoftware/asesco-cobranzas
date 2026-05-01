@extends('layouts.app')

@section('title', 'Cargues — Asignación Terceros')
@section('page-title', 'Asignación Terceros')

@section('content')
<div x-data="cargueTelefonos()" class="space-y-5">

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Total Registros</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_registros']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Titulares</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_titulares']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Terceros</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_terceros']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Empresas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_empresas']) }}</p>
        </div>
    </div>

    {{-- Upload + Search bar --}}
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Upload button --}}
            <label class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer"
                   :class="uploading ? 'opacity-50 pointer-events-none' : ''">
                <template x-if="!uploading">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </template>
                <template x-if="uploading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <span x-text="uploading ? 'Procesando...' : 'Subir XLSX'"></span>
                <input type="file" accept=".xlsx,.xls" @change="subirArchivo($event)" class="hidden" :disabled="uploading">
            </label>

            {{-- Divider --}}
            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="buscar" @input.debounce.400ms="cargar(1)"
                       placeholder="Buscar por cédula, nombre, empresa, teléfono..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
            </div>

            {{-- Filter: Calidad --}}
            <select x-model="filtroCalidad" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todos</option>
                <option value="TT">Titular</option>
                <option value="CD">Codeudor</option>
            </select>

            {{-- Filter: Tipo dato --}}
            <select x-model="filtroTipo" @change="cargar(1)"
                    class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                <option value="">Todos los tipos</option>
                <option value="celular">Celular</option>
                <option value="fijo">Fijo</option>
                <option value="correo">Correo</option>
            </select>
        </div>
    </div>

    {{-- Upload result message --}}
    <template x-if="uploadResult">
        <div class="rounded-xl border px-4 py-3 flex items-center gap-3"
             :class="uploadResult.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
            <template x-if="uploadResult.success">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </template>
            <template x-if="!uploadResult.success">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            </template>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium" :class="uploadResult.success ? 'text-green-700' : 'text-red-700'" x-text="uploadResult.message"></p>
                <template x-if="uploadResult.success">
                    <div class="flex items-center gap-4 mt-1">
                        <span class="text-xs text-green-600"><strong x-text="uploadResult.nuevos"></strong> nuevos</span>
                        <span class="text-xs text-gray-500"><strong x-text="uploadResult.duplicados"></strong> duplicados</span>
                        <span class="text-xs text-red-500" x-show="uploadResult.errores > 0"><strong x-text="uploadResult.errores"></strong> errores</span>
                    </div>
                </template>
            </div>
            <button @click="uploadResult = null" class="p-1 rounded hover:bg-black/5 transition-colors cursor-pointer">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>

    {{-- Loading --}}
    <template x-if="cargando">
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="flex flex-col items-center justify-center">
                <div class="relative w-12 h-12 mb-3">
                    <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-asesco-orange border-t-transparent animate-spin"></div>
                </div>
                <p class="text-sm text-gray-500">Cargando registros...</p>
            </div>
        </div>
    </template>

    {{-- Table --}}
    <template x-if="!cargando && registros.length > 0">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Referencia</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cédula Tercero</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre Tercero</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Calidad</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dato</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, i) in registros" :key="row.id">
                            <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors"
                                :class="i % 2 === 1 ? 'bg-gray-50/30' : ''">
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700" x-text="row.referencia"></td>
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700" x-text="row.cedula_tercero"></td>
                                <td class="px-4 py-2.5 text-sm text-gray-700 font-medium" x-text="row.nombre_tercero"></td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                          :class="row.calidad === 'TT' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-purple-50 text-purple-600 border border-purple-100'"
                                          x-text="row.calidad === 'TT' ? 'Titular' : 'Codeudor'"></span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-gray-600" x-text="row.empresa"></td>
                                <td class="px-4 py-2.5 text-sm font-mono text-gray-700" x-text="row.dato"></td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                          :class="{
                                              'bg-green-50 text-green-600 border border-green-100': row.tipo_dato === 'celular',
                                              'bg-amber-50 text-amber-600 border border-amber-100': row.tipo_dato === 'fijo',
                                              'bg-sky-50 text-sky-600 border border-sky-100': row.tipo_dato === 'correo',
                                          }"
                                          x-text="row.tipo_dato"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    Mostrando <strong x-text="paginacion.from || 0"></strong> a <strong x-text="paginacion.to || 0"></strong> de <strong x-text="paginacion.total || 0"></strong> registros
                </p>
                <div class="flex items-center gap-1">
                    <button @click="cargar(paginacion.current_page - 1)" :disabled="!paginacion.prev_page_url"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        Anterior
                    </button>
                    <span class="px-3 py-1.5 text-xs font-semibold text-asesco-orange">
                        Pág. <span x-text="paginacion.current_page"></span> / <span x-text="paginacion.last_page"></span>
                    </span>
                    <button @click="cargar(paginacion.current_page + 1)" :disabled="!paginacion.next_page_url"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="!cargando && registros.length === 0 && cargado">
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400 text-sm" x-text="buscar ? 'No se encontraron registros con ese criterio de búsqueda' : 'No hay registros cargados aún. Sube un archivo XLSX para comenzar.'"></p>
        </div>
    </template>

</div>

@push('scripts')
<script>
function cargueTelefonos() {
    return {
        registros: [],
        paginacion: {},
        buscar: '',
        filtroCalidad: '',
        filtroTipo: '',
        cargando: false,
        cargado: false,
        uploading: false,
        uploadResult: null,

        init() {
            this.cargar(1);
        },

        async cargar(page = 1) {
            this.cargando = true;
            try {
                const params = new URLSearchParams();
                params.set('page', page);
                if (this.buscar) params.set('buscar', this.buscar);
                if (this.filtroCalidad) params.set('calidad', this.filtroCalidad);
                if (this.filtroTipo) params.set('tipo_dato', this.filtroTipo);

                const res = await fetch(`{{ route('cargues.telefonos.listar') }}?${params}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.registros = data.data;
                this.paginacion = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    from: data.from,
                    to: data.to,
                    total: data.total,
                    prev_page_url: data.prev_page_url,
                    next_page_url: data.next_page_url,
                };
            } catch (e) {
                console.error('Error cargando registros:', e);
            } finally {
                this.cargando = false;
                this.cargado = true;
            }
        },

        async subirArchivo(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.uploading = true;
            this.uploadResult = null;

            const formData = new FormData();
            formData.append('archivo', file);

            try {
                const res = await fetch('{{ route('cargues.telefonos.importar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await res.json();
                this.uploadResult = data;

                if (data.success) {
                    this.cargar(1);
                }
            } catch (e) {
                this.uploadResult = { success: false, message: 'Error de conexión: ' + e.message };
            } finally {
                this.uploading = false;
                // Reset file input
                event.target.value = '';
            }
        },
    };
}
</script>
@endpush
@endsection
