@extends('layouts.app')

@section('title', 'Roles y Permisos')
@section('page-title', 'Roles y Permisos')

@section('content')
<div x-data="rolesPage()" class="space-y-5">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

        {{-- ─── Columna izquierda: Lista de roles ──────────────────── --}}
        <div class="lg:col-span-2 space-y-3">

            {{-- Botón crear --}}
            <button @click="showCreate = true"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-asesco-orange to-asesco-coral text-white text-sm font-semibold rounded-xl shadow-md shadow-asesco-orange/20 hover:shadow-lg hover:shadow-asesco-orange/30 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Rol
            </button>

            {{-- Tarjetas de roles --}}
            <template x-for="role in roles" :key="role.id">
                <div @click="selectRole(role)"
                     class="bg-white rounded-xl border transition-all duration-200 p-4 cursor-pointer select-none"
                     :class="selectedRole?.id === role.id
                         ? 'border-asesco-orange shadow-md shadow-asesco-orange/10 ring-1 ring-asesco-orange/20'
                         : 'border-gray-200 hover:border-gray-300 hover:shadow-sm'">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            {{-- Ícono --}}
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                                 :class="role.name === 'admin'
                                     ? 'bg-asesco-orange/10 text-asesco-orange'
                                     : (selectedRole?.id === role.id ? 'bg-asesco-orange/10 text-asesco-orange' : 'bg-gray-100 text-gray-400')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 capitalize" x-text="role.name"></p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <span x-text="role.permissions.length"></span> permiso(s) &middot;
                                    <span x-text="role.users_count"></span> usuario(s)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <template x-if="role.name === 'admin'">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-asesco-orange/10 text-asesco-orange border border-asesco-orange/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Admin
                                </span>
                            </template>
                            <svg class="w-4 h-4 transition-all duration-200"
                                 :class="selectedRole?.id === role.id ? 'text-asesco-orange rotate-90' : 'text-gray-300'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Mini badges de permisos --}}
                    <div class="flex flex-wrap gap-1 mt-3" x-show="role.permissions.length > 0">
                        <template x-for="(perm, i) in role.permissions.slice(0, 6)" :key="perm">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wide bg-gray-100 text-gray-500"
                                  x-text="perm.split('.')[1]"></span>
                        </template>
                        <template x-if="role.permissions.length > 6">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-semibold bg-gray-100 text-gray-400"
                                  x-text="'+' + (role.permissions.length - 6)"></span>
                        </template>
                        <template x-if="role.permissions.length === 0">
                            <span class="text-[10px] text-gray-300 italic">Sin permisos asignados</span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- ─── Columna derecha: Editor de permisos ─────────────────── --}}
        <div class="lg:col-span-3">

            {{-- Estado vacío --}}
            <template x-if="!selectedRole">
                <div class="bg-white rounded-xl border border-gray-200 p-14 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-400">Selecciona un rol para gestionar sus permisos</p>
                    <p class="text-xs text-gray-300 mt-1">Haz clic en cualquier rol de la lista de la izquierda</p>
                </div>
            </template>

            {{-- Editor --}}
            <template x-if="selectedRole">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

                    {{-- Header del editor --}}
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                 :class="selectedRole.name === 'admin' ? 'bg-asesco-orange/10 text-asesco-orange' : 'bg-gray-100 text-gray-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-gray-800 capitalize truncate" x-text="selectedRole.name"></h3>
                                <p class="text-xs text-gray-400"
                                   x-text="selectedRole.name === 'admin' ? 'Rol protegido — acceso completo' : 'Activa o desactiva los permisos de este rol'"></p>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Admin: solo lectura --}}
                            <template x-if="selectedRole.name === 'admin'">
                                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Solo lectura
                                </span>
                            </template>

                            {{-- Roles editables: borrar + guardar --}}
                            <template x-if="selectedRole.name !== 'admin'">
                                <div class="flex items-center gap-2">
                                    <button @click="deleteRole(selectedRole)"
                                            :disabled="deleting"
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span x-text="deleting ? 'Eliminando...' : 'Eliminar'"></span>
                                    </button>
                                    <button @click="savePermissions()"
                                            :disabled="saving"
                                            class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-asesco-orange to-asesco-coral text-white shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <template x-if="!saving">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </template>
                                        <template x-if="saving">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="saving ? 'Guardando...' : 'Guardar cambios'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Mensaje de éxito/error --}}
                    <template x-if="message">
                        <div class="mx-5 mt-4 px-4 py-2.5 rounded-lg text-sm flex items-center gap-2 border"
                             :class="message.success
                                 ? 'bg-green-50 text-green-700 border-green-200'
                                 : 'bg-red-50 text-red-700 border-red-200'">
                            <template x-if="message.success">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <template x-if="!message.success">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <span x-text="message.text"></span>
                        </div>
                    </template>

                    {{-- Grupos de permisos --}}
                    <div class="p-5 space-y-4">
                        <template x-for="group in permissionGroups" :key="group.name">
                            <div class="rounded-xl border border-gray-100 overflow-hidden">
                                {{-- Cabecera del grupo --}}
                                <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider" x-text="group.name"></h4>
                                    <template x-if="selectedRole.name !== 'admin'">
                                        <button @click="toggleGroup(group.permissions)"
                                                class="text-[10px] font-semibold text-asesco-orange hover:text-asesco-coral transition-colors cursor-pointer"
                                                x-text="groupAllSelected(group.permissions) ? 'Quitar todos' : 'Seleccionar todos'">
                                        </button>
                                    </template>
                                </div>
                                {{-- Checkboxes --}}
                                <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <template x-for="perm in group.permissions" :key="perm">
                                        <label class="flex items-center gap-2.5 rounded-lg p-1.5 -m-1.5 transition-colors"
                                               :class="selectedRole.name === 'admin'
                                                   ? 'cursor-default'
                                                   : 'cursor-pointer hover:bg-gray-50'"
                                               @click.prevent="selectedRole.name !== 'admin' && togglePermission(perm)">
                                            {{-- Checkbox personalizado --}}
                                            <div class="rounded flex items-center justify-center shrink-0 border-2 transition-all duration-150"
                                                 style="width:18px;height:18px;"
                                                 :class="hasPermission(perm)
                                                     ? 'bg-asesco-orange border-asesco-orange'
                                                     : (selectedRole.name !== 'admin'
                                                         ? 'bg-white border-gray-300 hover:border-asesco-orange/50'
                                                         : 'bg-gray-100 border-gray-200')">
                                                <template x-if="hasPermission(perm)">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                            </div>
                                            <span class="text-sm text-gray-700 leading-tight"
                                                  x-text="permissionLabels[perm] || perm"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ─── Modal: Crear rol ──────────────────────────────────────── --}}
    <template x-if="showCreate">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background: rgba(0,0,0,0.5);"
             @click.self="closeCreate()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-asesco-orange/10 text-asesco-orange flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Nuevo Rol</h3>
                    </div>
                    <button @click="closeCreate()"
                            class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nombre del rol</label>
                    <input type="text"
                           x-model="newRoleName"
                           @keyup.enter="createRole()"
                           placeholder="ej: auditor, coordinador..."
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-asesco-orange/20 focus:border-asesco-orange focus:bg-white transition-all"
                           x-ref="createInput">
                    <p class="text-[11px] text-gray-400 mt-1.5">Solo letras, números y guiones bajos. Se guardará en minúsculas.</p>
                    <template x-if="createError">
                        <p class="text-xs text-red-600 mt-2 font-medium" x-text="createError"></p>
                    </template>
                </div>

                <div class="flex gap-3">
                    <button @click="closeCreate()"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all cursor-pointer">
                        Cancelar
                    </button>
                    <button @click="createRole()"
                            :disabled="creating || !newRoleName.trim()"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-asesco-orange to-asesco-coral shadow-sm hover:shadow-md transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="creating ? 'Creando...' : 'Crear rol'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
function rolesPage() {
    return {
        roles: @json($rolesJson),
        permissionGroups: @json($permissionGroupsAlpine),
        permissionLabels: @json($permissionLabels),

        selectedRole: null,
        editingPermissions: [],

        showCreate: false,
        newRoleName: '',
        createError: '',

        saving: false,
        deleting: false,
        creating: false,
        message: null,

        selectRole(role) {
            this.selectedRole = role;
            this.editingPermissions = [...role.permissions];
            this.message = null;
        },

        hasPermission(permName) {
            return this.editingPermissions.includes(permName);
        },

        togglePermission(permName) {
            const idx = this.editingPermissions.indexOf(permName);
            if (idx > -1) {
                this.editingPermissions.splice(idx, 1);
            } else {
                this.editingPermissions.push(permName);
            }
        },

        groupAllSelected(perms) {
            return perms.every(p => this.editingPermissions.includes(p));
        },

        toggleGroup(perms) {
            if (this.groupAllSelected(perms)) {
                this.editingPermissions = this.editingPermissions.filter(p => !perms.includes(p));
            } else {
                perms.forEach(p => {
                    if (!this.editingPermissions.includes(p)) {
                        this.editingPermissions.push(p);
                    }
                });
            }
        },

        async savePermissions() {
            if (!this.selectedRole || this.selectedRole.name === 'admin') return;
            this.saving = true;
            this.message = null;
            try {
                const res = await fetch(`{{ url('/roles') }}/${this.selectedRole.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ permissions: this.editingPermissions }),
                });
                const data = await res.json();
                if (data.success) {
                    const idx = this.roles.findIndex(r => r.id === this.selectedRole.id);
                    if (idx > -1) {
                        this.roles[idx].permissions = data.permissions;
                        this.selectedRole = this.roles[idx];
                        this.editingPermissions = [...data.permissions];
                    }
                    this.message = { success: true, text: 'Permisos guardados correctamente.' };
                    setTimeout(() => this.message = null, 4000);
                } else {
                    this.message = { success: false, text: data.message ?? 'Error al guardar.' };
                }
            } catch (e) {
                this.message = { success: false, text: 'Error de conexión.' };
            } finally {
                this.saving = false;
            }
        },

        async createRole() {
            if (!this.newRoleName.trim() || this.creating) return;
            this.creating = true;
            this.createError = '';
            try {
                const res = await fetch('{{ route('roles.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: this.newRoleName.trim() }),
                });
                const data = await res.json();
                if (data.success) {
                    this.roles.push(data.role);
                    this.closeCreate();
                    this.$nextTick(() => this.selectRole(data.role));
                } else {
                    this.createError = data.message
                        ?? data.errors?.name?.[0]
                        ?? 'Error al crear el rol.';
                }
            } catch (e) {
                this.createError = 'Error de conexión.';
            } finally {
                this.creating = false;
            }
        },

        closeCreate() {
            this.showCreate = false;
            this.newRoleName = '';
            this.createError = '';
        },

        async deleteRole(role) {
            if (role.name === 'admin') return;
            if (!confirm(`¿Eliminar el rol "${role.name}"?\n\nEsta acción no se puede deshacer.`)) return;
            this.deleting = true;
            this.message = null;
            try {
                const res = await fetch(`{{ url('/roles') }}/${role.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.roles = this.roles.filter(r => r.id !== role.id);
                    this.selectedRole = null;
                    this.editingPermissions = [];
                } else {
                    this.message = { success: false, text: data.message };
                }
            } catch (e) {
                this.message = { success: false, text: 'Error de conexión.' };
            } finally {
                this.deleting = false;
            }
        },
    };
}
</script>
@endpush
@endsection
