<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    private const ADMIN_ROLE = 'admin';

    private array $permissionGroups = [
        'Usuarios' => [
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
        ],
        'Gestiones' => [
            'consultas.ver',
            'consultas.crear',
            'consultas.exportar',
        ],
        'Sistemas EPS' => [
            'sistemas.ver',
            'sistemas.crear',
            'sistemas.editar',
            'sistemas.eliminar',
        ],
        'Cargues' => [
            'cargues.ver',
            'cargues.importar',
        ],
    ];

    private array $permissionLabels = [
        'usuarios.ver'       => 'Ver',
        'usuarios.crear'     => 'Crear',
        'usuarios.editar'    => 'Editar',
        'usuarios.eliminar'  => 'Eliminar',
        'consultas.ver'      => 'Ver',
        'consultas.crear'    => 'Registrar gestión',
        'consultas.exportar' => 'Exportar',
        'sistemas.ver'       => 'Ver',
        'sistemas.crear'     => 'Crear',
        'sistemas.editar'    => 'Editar',
        'sistemas.eliminar'  => 'Eliminar',
        'cargues.ver'        => 'Ver',
        'cargues.importar'   => 'Importar',
    ];

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'permissions' => $r->permissions->pluck('name')->values(),
                'users_count' => $r->users()->count(),
            ]);

        $permissionGroupsForAlpine = collect($this->permissionGroups)
            ->map(fn($perms, $name) => ['name' => $name, 'permissions' => $perms])
            ->values();

        return view('roles.index', [
            'rolesJson'             => $roles,
            'permissionGroups'      => $this->permissionGroups,
            'permissionLabels'      => $this->permissionLabels,
            'permissionGroupsAlpine'=> $permissionGroupsForAlpine,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name', 'regex:/^[a-z0-9 _áéíóúüñ]+$/i'],
        ], [
            'name.unique' => 'Ya existe un rol con ese nombre.',
            'name.regex'  => 'El nombre solo puede tener letras, números, espacios y guiones bajos.',
        ]);

        $name = strtolower(trim($request->name));

        if ($name === self::ADMIN_ROLE) {
            return response()->json(['success' => false, 'message' => 'Ese nombre de rol está reservado.'], 422);
        }

        $role = Role::create(['name' => $name, 'guard_name' => 'web']);

        return response()->json([
            'success' => true,
            'role'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'permissions' => [],
                'users_count' => 0,
            ],
        ]);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        if ($role->name === self::ADMIN_ROLE) {
            return response()->json(['success' => false, 'message' => 'No se puede editar el rol admin.'], 403);
        }

        $request->validate([
            'permissions'   => 'present|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success'     => true,
            'permissions' => $role->fresh('permissions')->permissions->pluck('name')->values(),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === self::ADMIN_ROLE) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar el rol admin.'], 403);
        }

        $count = $role->users()->count();
        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: {$count} usuario(s) tienen este rol asignado.",
            ], 422);
        }

        $role->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json(['success' => true]);
    }
}
