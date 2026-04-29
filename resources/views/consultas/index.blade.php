@extends('layouts.app')

@section('title', 'Gestiones')
@section('page-title', 'Gestiones')

@section('content')
<div x-data="consultaPage()" class="space-y-5">

    {{-- Compact search bar --}}
    <div class="bg-white rounded-xl border border-gray-200 px-4 py-3">
        <div class="flex items-center gap-3">
            {{-- Search input --}}
            <div class="relative w-64 shrink-0">
                <input id="cedula" type="text" x-model="cedula" @keydown.enter.prevent="consultar()"
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                       :disabled="loading" inputmode="numeric" pattern="[0-9]*">
            </div>

            {{-- Search button (icon only) --}}
            <button @click="consultar()" :disabled="loading || !cedula.trim()"
                    class="flex items-center justify-center w-9 h-9 shrink-0 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    title="Consultar">
                <template x-if="!loading">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
            </button>

            {{-- Person name (inline) --}}
            <div x-show="personName" x-transition class="flex items-center gap-2 min-w-0" style="display:none">
                <div class="w-px h-6 bg-gray-200 shrink-0"></div>
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="text-xs font-medium text-gray-400 shrink-0">Nombre:</span>
                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="personName"></p>
                </div>
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

            {{-- Pill-style tab bar --}}
            <div class="flex items-center gap-1.5 px-4 py-3 bg-gray-50/80 border-b border-gray-200">
                <button @click="activeTab = 'localizacion'"
                        :class="activeTab === 'localizacion'
                            ? 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Localización
                </button>
                <button @click="activeTab = 'comentarios'"
                        :class="activeTab === 'comentarios'
                            ? 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Comentarios
                </button>
                <button @click="activeTab = 'telefonos'"
                        :class="activeTab === 'telefonos'
                            ? 'bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-md shadow-asesco-orange/20'
                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/60'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
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
                        <table class="w-full text-sm min-w-[780px]">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Departamento</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Municipio</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de Consulta</th>
                                    <th class="w-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, i) in flatRecords" :key="row._systemSlug + '-' + row._recordIdx">
                                    <tr class="border-b border-gray-100 hover:bg-orange-50/30 transition-colors"
                                        :class="i % 2 === 1 ? 'bg-gray-50/30' : ''">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-gray-700" x-text="row._systemName"></span>
                                                <span x-show="row._totalInSystem > 1"
                                                      class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono bg-orange-50 text-orange-500 border border-orange-100">
                                                    #<span x-text="row._recordIdx + 1"></span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getDepartamento(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getMunicipio(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-600" x-text="getTelefono(row)"></td>
                                        <td class="px-4 py-3 text-sm text-gray-500" x-text="row._consultedAt || '—'"></td>
                                        <td class="px-4 py-3">
                                            <button @click="openModal(row)"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-asesco-orange hover:bg-orange-50 transition-colors"
                                                    title="Ver detalle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.58-3.007-9.964-7.178z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            {{-- Tab: Comentarios --}}
            <div x-show="activeTab === 'comentarios'" class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-600 mb-1">Se está trabajando</p>
                <p class="text-xs text-gray-400">Módulo en desarrollo</p>
            </div>

            {{-- Tab: Teléfonos --}}
            <div x-show="activeTab === 'telefonos'" class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-600 mb-1">Se está trabajando</p>
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

    {{-- Detail Modal --}}
    <style>
        .modal-overlay-centered {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: fixed !important;
            inset: 0 !important;
            z-index: 9999 !important;
            padding: 16px !important;
            background: rgba(15,23,42,0.55) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
        }
        .modal-overlay-centered[x-cloak],
        .modal-overlay-centered[style*="display: none"] {
            display: none !important;
        }
    </style>
    <template x-teleport="body">
        <div x-show="modal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="modal = false"
             @click.self="modal = false"
             class="modal-overlay-centered">
            {{-- Modal card --}}
            <div x-show="modal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 style="width:100%;max-width:400px;display:flex;flex-direction:column;border-radius:14px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.35);">
                {{-- Dark header --}}
                <div style="background:#1e2532;padding:14px 18px 12px;flex-shrink:0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:3px;">
                            <svg style="width:11px;height:11px;flex-shrink:0;" fill="none" stroke="#f97316" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                            </svg>
                            <p style="font-size:10px;color:#f97316;text-transform:uppercase;letter-spacing:.08em;font-weight:700;" x-text="modalRecord ? modalRecord._systemName : ''"></p>
                        </div>
                        <p style="font-size:13px;font-weight:600;color:#f1f5f9;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="modalRecord ? (getNombres(modalRecord) + ' ' + getApellidos(modalRecord)).trim() : ''"></p>
                        <p style="font-size:10px;color:#64748b;margin-top:2px;font-family:monospace;" x-text="modalRecord ? getCedulaField(modalRecord) : ''"></p>
                    </div>
                    <button @click="modal = false" style="flex-shrink:0;width:26px;height:26px;border-radius:7px;border:none;background:rgba(255,255,255,0.08);color:#94a3b8;display:flex;align-items:center;justify-content:center;cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div style="flex:1;overflow-y:auto;background:#ffffff;">
                    <template x-if="modalRecord">
                        <div>
                            <template x-for="(value, key) in modalRecord" :key="key">
                                <template x-if="!String(key).startsWith('_') && !isObject(value) && !isArray(value) && hasValue(value)">
                                    <div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:7px 18px;border-bottom:1px solid #f1f5f9;">
                                        <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;flex-shrink:0;width:88px;line-height:1.3;" x-text="formatLabel(key)"></p>
                                        <p style="font-size:11px;color:#1e293b;font-weight:500;text-align:right;word-break:break-all;" x-text="formatValue(value)"></p>
                                    </div>
                                </template>
                            </template>
                            <template x-for="(value, key) in modalRecord" :key="'s-' + key">
                                <template x-if="isObject(value)">
                                    <div>
                                        <div style="padding:5px 18px;background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                                            <p style="font-size:9px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.08em;" x-text="formatLabel(key)"></p>
                                        </div>
                                        <template x-for="(subVal, subKey) in value" :key="subKey">
                                            <template x-if="hasValue(subVal)">
                                                <div style="display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:7px 18px;border-bottom:1px solid #f1f5f9;">
                                                    <p style="font-size:9px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;flex-shrink:0;width:88px;line-height:1.3;" x-text="formatLabel(subKey)"></p>
                                                    <p style="font-size:11px;color:#1e293b;font-weight:500;text-align:right;word-break:break-all;" x-text="formatValue(subVal)"></p>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
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
        flatRecords: [],
        searchedCedula: '',
        personName: null,
        activeTab: 'localizacion',
        modal: false,
        modalRecord: null,
        systemCount: {{ $systemCount }},

        async consultar() {
            const c = this.cedula.trim();
            if (!c || this.loading) return;

            this.loading = true;
            this.results = null;
            this.flatRecords = [];
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
                // Normalize each result into a records array
                data.results = data.results.map(r => {
                    const rawData = r.data;
                    const records = rawData
                        ? (Array.isArray(rawData) ? rawData : [rawData])
                        : [];
                    return { ...r, records };
                });

                // Flatten all found records for the table
                const now = new Date();
                const consultedAt = now.toLocaleDateString('es-CO', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
                this.flatRecords = data.results
                    .filter(r => r.found && r.records.length > 0)
                    .flatMap(r => r.records.map((rec, idx) => ({
                        ...rec,
                        _systemName: r.name,
                        _systemSlug: r.slug,
                        _recordIdx: idx,
                        _totalInSystem: r.records.length,
                        _consultedAt: consultedAt,
                    })));

                this.results = data;
                this.personName = this.extractPersonName(data.results);
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            } finally {
                this.loading = false;
            }
        },

        openModal(row) {
            this.modalRecord = row;
            this.modal = true;
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

        getDepartamento(rec) {
            const val = rec.departamento || rec.depto || rec.dept || '';
            return (val && !/^\d+$/.test(String(val).trim())) ? val : '—';
        },

        getMunicipio(rec) {
            const val = rec.municipio || rec.ciudad || rec.city || '';
            return (val && !/^\d+$/.test(String(val).trim())) ? val : '—';
        },

        getTelefono(rec) {
            if (rec.telefono) return rec.telefono;
            if (rec.telefonos) return rec.telefonos;
            if (rec.celular) return rec.celular;
            if (rec.telefono_1) return rec.telefono_1;
            if (rec.phone) return rec.phone;
            return '—';
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

        hasValue(value) {
            return value !== null && value !== undefined && value !== '';
        },

        formatValue(value) {
            if (!this.hasValue(value)) return '';
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
