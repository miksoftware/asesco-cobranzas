<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos de usuarios
        Permission::firstOrCreate(['name' => 'usuarios.ver']);
        Permission::firstOrCreate(['name' => 'usuarios.crear']);
        Permission::firstOrCreate(['name' => 'usuarios.editar']);
        Permission::firstOrCreate(['name' => 'usuarios.eliminar']);

        // Permisos de consultas
        Permission::firstOrCreate(['name' => 'consultas.ver']);
        Permission::firstOrCreate(['name' => 'consultas.crear']);
        Permission::firstOrCreate(['name' => 'consultas.exportar']);

        // Permisos de sistemas EPS
        Permission::firstOrCreate(['name' => 'sistemas.ver']);
        Permission::firstOrCreate(['name' => 'sistemas.crear']);
        Permission::firstOrCreate(['name' => 'sistemas.editar']);
        Permission::firstOrCreate(['name' => 'sistemas.eliminar']);

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $supervisor = Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor->syncPermissions([
            'usuarios.ver',
            'consultas.ver',
            'consultas.crear',
            'consultas.exportar',
            'sistemas.ver',
        ]);

        $operador = Role::firstOrCreate(['name' => 'operador']);
        $operador->syncPermissions([
            'consultas.ver',
            'consultas.crear',
        ]);
    }
}
