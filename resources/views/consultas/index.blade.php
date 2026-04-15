@extends('layouts.app')

@section('title', 'Localización')
@section('page-title', 'Localización')

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
        <p class="text-xs text-gray-400 mt-3">
            <span class="inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Se consultarán <strong x-text="systemCount">{{ $systemCount }}</strong> sistemas de forma simultánea
            </span>
        </p>
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

    {{-- Results --}}
    <template x-if="results && !loading">
        <div class="space-y-4">
            {{-- Summary --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-asesco-orange/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-asesco-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Cédula: <span x-text="searchedCedula" class="font-mono"></span></p>
                            <p class="text-xs text-gray-400">
                                Encontrado en <span x-text="results.found" class="font-semibold text-green-600"></span> de <span x-text="results.total"></span> sistemas
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Result cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <template x-for="result in results.results" :key="result.slug">
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Card header --}}
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between"
                             :class="result.found ? 'bg-green-50/50' : (result.error ? 'bg-red-50/50' : 'bg-gray-50/50')">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :class="result.found ? 'bg-green-500' : (result.error ? 'bg-red-400' : 'bg-gray-300')"></span>
                                <h4 class="text-sm font-semibold text-gray-800" x-text="result.name"></h4>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full"
                                  :class="result.found ? 'bg-green-100 text-green-700' : (result.error ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500')"
                                  x-text="result.found ? 'Encontrado' : (result.error ? 'Error' : 'No encontrado')"></span>
                        </div>

                        {{-- Card body --}}
                        <div class="p-5">
                            <template x-if="result.found && result.data">
                                <div class="space-y-5">
                                    {{-- Simple fields --}}
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                        <template x-for="(value, key) in result.data" :key="key">
                                            <template x-if="!isObject(value)">
                                                <div>
                                                    <p class="text-[10px] uppercase tracking-wider text-gray-400 mb-0.5" x-text="formatLabel(key)"></p>
                                                    <p class="text-sm text-gray-700 font-medium" x-text="formatValue(value)"></p>
                                                </div>
                                            </template>
                                        </template>
                                    </div>

                                    {{-- Nested object sections --}}
                                    <template x-for="(value, key) in result.data" :key="'section-' + key">
                                        <template x-if="isObject(value)">
                                            <div class="border-t border-gray-100 pt-4">
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3" x-text="formatLabel(key)"></h5>
                                                <div class="grid grid-cols-2 gap-x-4 gap-y-3">
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
                            </template>
                            <template x-if="!result.found && !result.error">
                                <p class="text-sm text-gray-400 text-center py-4">Sin registros para esta cédula</p>
                            </template>
                            <template x-if="result.error">
                                <div class="flex items-center gap-2 text-sm text-red-500 py-4">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    <span x-text="result.error"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
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
        systemCount: {{ $systemCount }},

        async consultar() {
            const c = this.cedula.trim();
            if (!c || this.loading) return;

            this.loading = true;
            this.results = null;
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

                this.results = await res.json();
            } catch (e) {
                alert('Error de conexión: ' + e.message);
            } finally {
                this.loading = false;
            }
        },

        formatLabel(key) {
            return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        isObject(value) {
            return value !== null && typeof value === 'object' && !Array.isArray(value);
        },

        formatValue(value) {
            if (value === null || value === undefined || value === '') return '—';
            if (typeof value === 'number' || typeof value === 'boolean') return String(value);
            if (typeof value === 'string') {
                // Match datetime patterns like "2026-04-14T10:30:00+00:00" or "2026-04-14 10:30:00"
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
