@extends('layouts.app')

@section('title', 'Sistemas EPS')
@section('page-title', 'Sistemas EPS')

@section('content')
<div x-data="sistemasPage()" class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Configura los endpoints de cada sistema EPS para las consultas concurrentes.</p>
        </div>
        @can('sistemas.crear')
        <button @click="openCreate()"
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Agregar Sistema
        </button>
        @endcan
    </div>

    {{-- Systems grid --}}
    @if($systems->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
        </svg>
        <p class="text-gray-400 text-sm">No hay sistemas configurados aún.</p>
        <p class="text-gray-300 text-xs mt-1">Agrega los endpoints de tus sistemas EPS para comenzar.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($systems as $system)
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow relative group">
            {{-- Status indicator --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $system->is_active ? 'bg-asesco-orange/10' : 'bg-gray-100' }} flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $system->is_active ? 'text-asesco-orange' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm">{{ $system->name }}</h3>
                        <p class="text-xs text-gray-400">{{ $system->slug }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $system->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    <span class="w-2 h-2 rounded-full {{ $system->is_active ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></span>
                    {{ $system->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            {{-- Details --}}
            <div class="space-y-2 text-xs">
                <div class="flex items-center gap-2 text-gray-500">
                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.813a4.5 4.5 0 00-1.242-7.244l-4.5-4.5a4.5 4.5 0 00-6.364 6.364L4.34 8.374"/>
                    </svg>
                    <span class="truncate font-mono">{{ $system->base_url }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-500">
                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Timeout: {{ $system->timeout }}s</span>
                </div>
                <div class="flex items-center gap-2 text-gray-500">
                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                    <span>Token: ••••••••</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                @can('sistemas.editar')
                <form method="POST" action="{{ route('sistemas.toggle', $system) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all cursor-pointer {{ $system->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                        {{ $system->is_active ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
                <button @click="openEdit({{ $system->toJson() }})"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-asesco-orange/10 text-asesco-orange hover:bg-asesco-orange/20 transition-all cursor-pointer">
                    Editar
                </button>
                @endcan
                @can('sistemas.eliminar')
                <form method="POST" action="{{ route('sistemas.destroy', $system) }}" class="inline ml-auto"
                      onsubmit="return confirm('¿Eliminar {{ $system->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg text-red-500 hover:bg-red-50 transition-all cursor-pointer">
                        Eliminar
                    </button>
                </form>
                @endcan
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Modal --}}
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.stop
             x-show="showModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="isEditing ? 'Editar Sistema' : 'Agregar Sistema EPS'"></h3>
                    <p class="text-xs text-gray-400 mt-0.5">Configura el endpoint y token de autenticación</p>
                </div>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="isEditing ? '{{ url('sistemas') }}/' + form.id : '{{ route('sistemas.store') }}'" method="POST" class="p-6 space-y-5">
                @csrf
                <template x-if="isEditing"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del sistema</label>
                    <input type="text" name="name" x-model="form.name" required placeholder="Ej: ADRES, Coosalud, Nueva EPS..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Base</label>
                    <input type="url" name="base_url" x-model="form.base_url" required placeholder="http://localhost:8001"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5" x-text="isEditing ? 'Token API (dejar vacío para no cambiar)' : 'Token API (Bearer)'"></label>
                    <input type="text" name="api_token" x-model="form.api_token" :required="!isEditing" placeholder="1|abc123..."
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Ruta del endpoint</label>
                        <input type="text" name="endpoint_path" x-model="form.endpoint_path" placeholder="/api/consulta/cedula/{cedula}"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Timeout (seg)</label>
                        <input type="number" name="timeout" x-model="form.timeout" min="5" max="60" placeholder="15"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
                    </div>
                </div>

                <template x-if="isEditing">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                        <select name="is_active" x-model="form.is_active"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </template>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg transition-all cursor-pointer">
                        <span x-text="isEditing ? 'Guardar' : 'Agregar Sistema'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sistemasPage() {
    return {
        showModal: false, isEditing: false,
        form: { id: null, name: '', base_url: '', api_token: '', endpoint_path: '/api/consulta/cedula/{cedula}', timeout: 15, is_active: '1' },
        openCreate() {
            this.isEditing = false;
            this.form = { id: null, name: '', base_url: '', api_token: '', endpoint_path: '/api/consulta/cedula/{cedula}', timeout: 15, is_active: '1' };
            this.showModal = true;
        },
        openEdit(system) {
            this.isEditing = true;
            this.form = { id: system.id, name: system.name, base_url: system.base_url, api_token: '', endpoint_path: system.endpoint_path, timeout: system.timeout, is_active: system.is_active ? '1' : '0' };
            this.showModal = true;
        }
    };
}
</script>
@endpush
@endsection
