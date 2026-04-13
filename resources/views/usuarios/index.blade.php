@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div x-data="usuariosPage()" class="space-y-6">

    {{-- Toolbar --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('usuarios.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..."
                           class="pl-10 pr-4 py-2.5 w-full sm:w-72 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all">
                </div>
                <select name="role" onchange="this.form.submit()"
                        class="py-2.5 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </form>

            @can('usuarios.crear')
            <button @click="openCreate()"
                    class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Usuario
            </button>
            @endcan
        </div>
    </div>

    {{-- Table card --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuario</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Rol</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Creado</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-50 hover:bg-asesco-orange/[0.02] transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-asesco-orange to-asesco-coral flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php $roleName = $user->roles->first()?->name ?? 'sin-rol'; @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                {{ $roleName === 'admin' ? 'bg-asesco-orange/10 text-asesco-orange ring-1 ring-asesco-orange/20' : '' }}
                                {{ $roleName === 'supervisor' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : '' }}
                                {{ $roleName === 'operador' ? 'bg-gray-50 text-gray-600 ring-1 ring-gray-200' : '' }}
                                {{ $roleName === 'sin-rol' ? 'bg-gray-50 text-gray-400 ring-1 ring-gray-100' : '' }}">
                                {{ ucfirst($roleName) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-600">
                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500">
                                    <span class="w-2 h-2 rounded-full bg-red-400"></span> Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">{{ $user->created_at->format('d M, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                @can('usuarios.editar')
                                <button @click="openEdit({{ $user->toJson() }}, '{{ $roleName }}')"
                                        class="p-2 rounded-lg text-gray-400 hover:bg-asesco-orange/10 hover:text-asesco-orange transition-all cursor-pointer" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endcan
                                @can('usuarios.eliminar')
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('usuarios.destroy', $user) }}"
                                      onsubmit="return confirm('¿Eliminar a {{ $user->name }}? Esta acción no se puede deshacer.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all cursor-pointer" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="text-sm text-gray-400">No se encontraron usuarios</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Crear/Editar --}}
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all" @click.stop
             x-show="showModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="isEditing ? 'Editar Usuario' : 'Nuevo Usuario'"></h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="isEditing ? 'Modifica los datos del usuario' : 'Completa los datos para crear un nuevo usuario'"></p>
                </div>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <form :action="isEditing ? '{{ url('usuarios') }}/' + form.id : '{{ route('usuarios.store') }}'" method="POST" class="p-6 space-y-5">
                @csrf
                <template x-if="isEditing"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo</label>
                    <input id="name" type="text" name="name" x-model="form.name" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                           placeholder="Ej: Juan Pérez">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input id="email" type="email" name="email" x-model="form.email" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                           placeholder="correo@ejemplo.com">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5" x-text="isEditing ? 'Nueva contraseña' : 'Contraseña'"></label>
                        <input id="password" type="password" name="password" x-model="form.password" :required="!isEditing" minlength="6"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                               :placeholder="isEditing ? 'Dejar vacío para no cambiar' : '••••••••'">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" x-model="form.password_confirmation"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Rol</label>
                        <select id="role" name="role" x-model="form.role" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                            <option value="">Seleccionar rol...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="isEditing">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                        <select id="is_active" name="is_active" x-model="form.is_active"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all cursor-pointer">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                @if($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 p-3 space-y-1">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
                @endif

                {{-- Modal footer --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="showModal = false"
                            class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-lg shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 transition-all cursor-pointer">
                        <span x-text="isEditing ? 'Guardar Cambios' : 'Crear Usuario'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function usuariosPage() {
    return {
        showModal: {{ $errors->any() ? 'true' : 'false' }},
        isEditing: false,
        form: { id: null, name: '', email: '', password: '', password_confirmation: '', role: '', is_active: '1' },
        openCreate() {
            this.isEditing = false;
            this.form = { id: null, name: '', email: '', password: '', password_confirmation: '', role: '', is_active: '1' };
            this.showModal = true;
        },
        openEdit(user, roleName) {
            this.isEditing = true;
            this.form = {
                id: user.id, name: user.name, email: user.email,
                password: '', password_confirmation: '',
                role: roleName, is_active: user.is_active ? '1' : '0',
            };
            this.showModal = true;
        }
    };
}
</script>
@endpush
@endsection
