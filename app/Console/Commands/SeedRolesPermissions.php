<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SeedRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-roles-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed roles and permissions for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating roles and permissions...');

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
            Role::findOrCreate($role);
            $this->info("Created role: {$role}");
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
            Permission::findOrCreate($permission);
            $this->info("Created permission: {$permission}");
        }

        // Assign all permissions to System role
        $systemRole = Role::findByName('System');
        $systemRole->syncPermissions(Permission::all());
        $this->info('Assigned all permissions to System role');

        // Assign all permissions to Super Admin role
        $superAdminRole = Role::findByName('Super Admin');
        $superAdminRole->syncPermissions(Permission::all());
        $this->info('Assigned all permissions to Super Admin role');

        // Give Administrator most permissions except system level ones
        $adminRole = Role::findByName('Administrator');
        $adminPermissions = Permission::whereNotIn('name', ['delete roles', 'delete permissions'])->get();
        $adminRole->syncPermissions($adminPermissions);
        $this->info('Assigned appropriate permissions to Administrator role');

        // Check for test user and assign role if exists
        $testUser = User::where('email', 'test@example.com')->first();
        if ($testUser) {
            $testUser->assignRole('System');
            $this->info('Assigned System role to test user');
        }

        $this->info('Roles and permissions have been created successfully');
    }
}
