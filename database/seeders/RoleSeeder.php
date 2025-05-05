<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'System',
            'Super Admin',
            'Administrator',
            'Academic Officer',
            'Finance Manager',
            'IT Manager',
            'Lecturer',
            'Librarian',
            'Parent',
            'Procurement Officer',
            'Security Officer',
            'Staff',
            'Store Manager',
            'Student'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Create basic permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to System role
        $systemRole = Role::findByName('System');
        $systemRole->givePermissionTo(Permission::all());

        // Assign all permissions to Super Admin role
        $superAdminRole = Role::findByName('Super Admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // Give Administrator most permissions except system level ones
        $adminRole = Role::findByName('Administrator');
        $adminPermissions = Permission::whereNotIn('name', ['delete roles', 'delete permissions'])->get();
        $adminRole->givePermissionTo($adminPermissions);
    }
}
