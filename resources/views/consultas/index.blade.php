@extends('layouts.app')

@section('title', 'Gestiones')
@section('page-title', 'Gestiones')

@section('content')
<div x-data="consultaPage()" class="space-y-6">

    {{-- Search card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
            <div class="flex-1 w-full">
                <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1.5">Número de cédula</label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="cedula" type="text" x-model="cedula" @keydown.enter.prevent="consultar()"
                           placeholder="Ingresa el número de cédula..."
                           class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-base text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                           :disabled="loading" inputmode="numeric" pattern="[0-9]*">
                </div>
            </div>
            <button @click="consultar()" :disabled="loading || !cedula.trim()"
                    class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-xl shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 whitespace-nowrap">
                <template x-if="!loading">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <span x-text="loading ? 'Consultando...' : 'Consultar'"></span>
            </button>
        </div>
    </div>

    {{-- Sticky person name banner --}}
    <div x-show="personName"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sticky top-0 z-20 -mx-6 px-6 py-3 bg-gradient-to-r from-asesco-orange to-asesco-coral shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] text-white/70 font-medium uppercase tracking-wider leading-none mb-0.5">Consultando</p>
                <p class="text-base font-bold text-white leading-tight" x-text="personName"></p>
            </div>
        </div>
    </div>

    {{-- Loading state --}}
    <template x-if="loading">
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="flex flex-col items-center justify-center">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-full border-4 border-gray-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-asesco-orange border-t-transparent animate-spin"></div>
                </div>
                <p class="text-sm font-medium text-gray-700">Consultando sistemas EPS...</p>
                <p class="text-xs text-gray-400 mt-1">Esto puede tomar unos segundos</p>
            </div>
        </div>
    </template>

    {{-- Results with tabs --}}
    <template x-if="results && !loading">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            {{-- Tab navigation --}}
            <div class="flex border-b border-gray-200">
                <button @click="activeTab = 'localizacion'"
                        :class="activeTab === 'localizacion' ? 'border-b-2 border-asesco-orange text-asesco-orange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-3.5 text-sm font-medium transition-colors">
                    Localización
                </button>
                <button @click="activeTab = 'comentarios'"
                        :class="activeTab === 'comentarios' ? 'border-b-2 border-asesco-orange text-asesco-orange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-3.5 text-sm font-medium transition-colors">
                    Comentarios
                </button>
                <button @click="activeTab = 'telefonos'"
                        :class="activeTab === 'telefonos' ? 'border-b-2 border-asesco-orange text-asesco-orange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-3.5 text-sm font-medium transition-colors">
                    Teléfonos
                </button>
            </div>

            {{-- Tab: Localización --}}
            <div x-show="activeTab === 'localizacion'">

                {{-- No results --}}
                <template x-if="results.found === 0">
                    <div class="p-10 text-center">
                        <svg class="w-14 h-14 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No se encontraron registros para esta cédula en ningún sistema</p>
                    </div>
                </template>

                {{-- Table --}}
                <template x-if="results.found > 0">
                    <div class="overflow-x-auto">

                        {{-- Column headers --}}
                        <div class="grid grid-cols-[180px_1fr_90px_140px_1fr_1fr_44px] bg-gray-50 border-b border-gray-200 min-w-[860px]">
                            <div class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistema EPS</div>
                            <div class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</div>
                            <div class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo Doc.</div>
                            <div class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">N° Documento</div>
                            <div class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombres</div>
                            <div class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Apellidos</div>
                            <div></div>
                        </div>

                        {{-- System rows --}}
                        <template x-for="result in results.results.filter(r => r.found || r.error)" :key="result.slug">
                            <div>
                                {{-- System group header --}}
                                <div class="flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 bg-orange-50/30 cursor-pointer hover:bg-orange-50/60 select-none min-w-[860px]"
                                     @click="result.expanded = !result.expanded">
                                    <svg :class="result.expanded ? 'rotate-90' : ''"
                                         class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="font-semibold text-gray-700 text-sm" x-text="result.name"></span>
                                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 text-xs font-medium"
                                          x-text="result.records.length + ' registro' + (result.records.length !== 1 ? 's' : '')"></span>
                                    <template x-if="result.error && !result.found">
                                        <span class="text-xs text-red-500 ml-1" x-text="result.error"></span>
                                    </template>
                                </div>

                                {{-- Record rows (shown when system is expanded) --}}
                                <div x-show="result.expanded"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100">
                                    <template x-for="(rec, recIdx) in result.records" :key="recIdx">
                                        <div>
                                            {{-- Record row --}}
                                            <div class="grid grid-cols-[180px_1fr_90px_140px_1fr_1fr_44px] border-b border-gray-100 cursor-pointer hover:bg-gray-50/60 select-none min-w-[860px]"
                                                 :class="rec._expanded ? 'bg-gray-50/40' : ''"
                                                 @click="rec._expanded = !rec._expanded">
                                                <div class="px-4 py-3 pl-10 flex items-center">
                                                    <span class="text-xs text-gray-400 font-mono">#<span x-text="recIdx + 1"></span></span>
                                                </div>
                                                <div class="px-4 py-3 flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getEstadoColor(rec)"></span>
                                                    <span class="text-sm text-gray-700 truncate" x-text="getEstado(rec)"></span>
                                                </div>
                                                <div class="px-4 py-3 flex items-center">
                                                    <span class="text-sm text-gray-600" x-text="getTipoDoc(rec)"></span>
                                                </div>
                                                <div class="px-4 py-3 flex items-center">
                                                    <span class="text-sm text-gray-700 font-mono" x-text="getCedulaField(rec)"></span>
                                                </div>
                                                <div class="px-4 py-3 flex items-center">
                                                    <span class="text-sm text-gray-700 font-medium truncate" x-text="getNombres(rec)"></span>
                                                </div>
                                                <div class="px-4 py-3 flex items-center">
                                                    <span class="text-sm text-gray-700 font-medium truncate" x-text="getApellidos(rec)"></span>
                                                </div>
                                                <div class="flex items-center justify-center px-2">
                                                    <svg :class="rec._expanded ? 'rotate-90' : ''"
                                                         class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            {{-- Detail panel --}}
                                            <div x-show="rec._expanded"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="bg-gray-50/70 border-b border-gray-200 px-12 py-5 min-w-[860px]">
                                                {{-- Simple fields --}}
                                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-3">
                                                    <template x-for="(value, key) in rec" :key="key">
                                                        <template x-if="!String(key).startsWith('_') && !isObject(value) && !isArray(value)">
                                                            <div>
                                                                <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-0.5" x-text="formatLabel(key)"></p>
                                                                <p class="text-sm text-gray-700 font-medium" x-text="formatValue(value)"></p>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </div>
                                                {{-- Nested object sections --}}
                                                <template x-for="(value, key) in rec" :key="'sec-' + key">
                                                    <template x-if="isObject(value)">
                                                        <div class="mt-4 border-t border-gray-200 pt-4">
                                                            <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" x-text="formatLabel(key)"></h5>
                                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-3">
                                                                <template x-for="(subVal, subKey) in value" :key="subKey">
                                                                    <div>
                                                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-0.5" x-text="formatLabel(subKey)"></p>
                                                                        <p class="text-sm text-gray-700 font-medium" x-text="formatValue(subVal)"></p>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Tab: Comentarios --}}
            <div x-show="activeTab === 'comentarios'" class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-1">Se está trabajando</p>
                <p class="text-xs text-gray-400">Módulo en desarrollo</p>
            </div>

            {{-- Tab: Teléfonos --}}
            <div x-show="activeTab === 'telefonos'" class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-1">Se está trabajando</p>
                <p class="text-xs text-gray-400">Módulo en desarrollo</p>
            </div>

        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="!results && !loading">
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-20 h-20 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="0.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">Ingresa una cédula para consultar en todos los sistemas EPS</p>
        </div>
    </template>
</div>

@push('scripts')
<script>
function consultaPage() {
    return {
        cedula: '',
        loading: false,
        results: null,
        searchedCedula: '',
        personName: null,
        activeTab: 'localizacion',
        systemCount: {{ $systemCount }},

        async consultar() {
            const c = this.cedula.trim();
            if (!c || this.loading) return;

            this.loading = true;
            this.results = null;
            this.personName = null;
            this.activeTab = 'localizacion';
            this.searchedCedula = c;

            try {
                const res = await fetch('{{ route("consultas.consultar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ cedula: c }),
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    alert(err.message || 'Error al consultar');
                    return;
                }

                const data = await res.json();
                // Normalize: each result gets an array of records + expanded state
                data.results = data.results.map(r => {
                    const rawData = r.data;
                    const records = rawData
                        ? (Array.isArray(rawData) ? rawData : [rawData]).map(rec => ({ ...rec, _expanded: false }))
                        : [];
                    return { ...r, expanded: true, records };
                });
                this.results = data;
                this.personName = this.extractPersonName(data.results);
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            } finally {
                this.loading = false;
            }
        },

        extractPersonName(results) {
            const found = results.find(r => r.found && r.records && r.records.length > 0);
            if (!found) return null;
            const d = found.records[0];
            const parts = [d.primer_nombre, d.segundo_nombre, d.primer_apellido, d.segundo_apellido]
                .filter(v => v && String(v).trim());
            if (parts.length) return parts.join(' ');
            if (d.nombres || d.apellidos) return [d.nombres, d.apellidos].filter(Boolean).join(' ');
            if (d.nombre_completo) return d.nombre_completo;
            return null;
        },

        getEstado(rec) {
            return rec.estado_afiliado || rec.estado_afiliacion || rec.estado || rec.estado_detallado || '—';
        },

        getEstadoColor(rec) {
            const estado = (this.getEstado(rec) || '').toUpperCase();
            if (estado.includes('ACTIVO')) return 'bg-green-500';
            if (estado.includes('SUSPENDIDO') || estado.includes('RETIRADO') || estado.includes('SIN DERECHO')) return 'bg-red-400';
            return 'bg-gray-400';
        },

        getTipoDoc(rec) {
            return rec.tipo_documento || rec.tipo_id || '—';
        },

        getCedulaField(rec) {
            return rec.cedula || rec.numero_documento || rec.identificacion || '—';
        },

        getNombres(rec) {
            const parts = [rec.primer_nombre, rec.segundo_nombre].filter(v => v && String(v).trim());
            if (parts.length) return parts.join(' ');
            return rec.nombres || (rec.nombre_completo ? rec.nombre_completo.split(' ').slice(0, 2).join(' ') : '—');
        },

        getApellidos(rec) {
            const parts = [rec.primer_apellido, rec.segundo_apellido].filter(v => v && String(v).trim());
            if (parts.length) return parts.join(' ');
            return rec.apellidos || (rec.nombre_completo ? rec.nombre_completo.split(' ').slice(2).join(' ') : '—');
        },

        formatLabel(key) {
            return String(key).replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        isObject(value) {
            return value !== null && typeof value === 'object' && !Array.isArray(value);
        },

        isArray(value) {
            return Array.isArray(value);
        },

        formatValue(value) {
            if (value === null || value === undefined || value === '') return '—';
            if (typeof value === 'boolean') return value ? 'Sí' : 'No';
            if (typeof value === 'number') return String(value);
            if (typeof value === 'string') {
                const dtMatch = value.match(/^(\d{4}-\d{2}-\d{2})[T\s]\d{2}:\d{2}/);
                if (dtMatch) return dtMatch[1];
            }
            return value;
        }
    };
}
</script>
@endpush
@endsection
