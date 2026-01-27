<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $limitedRole = Role::firstOrCreate(['name' => 'limited']);
        // Compatibilidad con el menú (usa "limited_user" como ability)
        $limitedUserRole = Role::firstOrCreate(['name' => 'limited_user']);

        // Permisos básicos
        $permissions = [
            'crear_user',
            'editar_user',
            'ver_user',
            // Ability usada por el menú para usuarios no-admin
            'limited_user',
            // Permisos para limited
            'dashboard',
            'inventario_limited',
            'presupuestos_limited',
            'ordenes_limited',
            'ventas_limited',
            // Auditoría (solo admin por defecto)
            'auditoria_ver'
        ];

        // Crear todos los permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar TODOS los permisos al rol admin
        $adminRole->syncPermissions(Permission::all());

        // Asignar solo permiso de ver_user al rol user
        $userRole->givePermissionTo('ver_user');

        // Asignar permisos al rol limited / limited_user
        $limitedPermissions = [
            'limited_user',
            'dashboard',
            'inventario_limited',
            'presupuestos_limited',
            'ordenes_limited',
            'ventas_limited'
        ];
        $limitedRole->syncPermissions($limitedPermissions);
        $limitedUserRole->syncPermissions($limitedPermissions);

        // Asignar roles basados en el campo 'rol' de la tabla users
        $this->assignRolesBasedOnRolField();
    }

    protected function assignRolesBasedOnRolField(): void
    {
        // Asignar rol admin
        User::where('rol', 'admin')->each(function ($user) {
            $user->syncRoles('admin');
        });

        // Asignar rol user
        User::where('rol', 'user')->each(function ($user) {
            $user->syncRoles('user');
        });

        // Asignar rol limited
        User::where('rol', 'limited')->each(function ($user) {
            $user->syncRoles('limited');
        });

        // Asignar rol limited_user (si existe)
        User::where('rol', 'limited_user')->each(function ($user) {
            $user->syncRoles('limited_user');
        });

        // Asignar rol admin al usuario por defecto si existe
        $defaultAdmin = User::where('name', 'admin')->first();
        if ($defaultAdmin) {
            $defaultAdmin->syncRoles('admin');
            $defaultAdmin->update(['rol' => 'admin']);
        }
    }
}
