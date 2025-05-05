<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, make sure all roles exist in the Spatie system
        $this->ensureRolesExist();
        
        // Map existing users from simple roles to Spatie roles
        $this->migrateUsersToSpatieRoles();
        
        // Remove the old role column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the role column back if the migration is reversed
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }
        });

        // Restore the old role values based on Spatie roles
        $this->restoreOldRoleValues();
    }

    /**
     * Ensure all required roles exist in the Spatie system
     */
    private function ensureRolesExist(): void
    {
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
            'Student',
        ];
        
        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }

    /**
     * Map users from the old simple role to the new Spatie roles
     */
    private function migrateUsersToSpatieRoles(): void
    {
        // Define mapping from old role values to new Spatie roles
        $roleMapping = [
            'admin' => 'Administrator',
            'superadmin' => 'Super Admin',
            'user' => 'Student',
            'student' => 'Student',
            'staff' => 'Staff',
            'lecturer' => 'Lecturer',
            'finance' => 'Finance Manager',
            'procurement' => 'Procurement Officer',
            'security' => 'Security Officer',
            'librarian' => 'Librarian',
            'store' => 'Store Manager',
            'parent' => 'Parent',
            'academic' => 'Academic Officer',
            'it' => 'IT Manager',
            'system' => 'System',
        ];
        
        // Process each user
        User::whereNotNull('role')->chunk(100, function ($users) use ($roleMapping) {
            foreach ($users as $user) {
                // Get the old role and find its mapping
                $oldRole = strtolower($user->role);
                $newRole = $roleMapping[$oldRole] ?? 'Student'; // Default to Student if no mapping
                
                // Assign the corresponding Spatie role
                $user->assignRole($newRole);
            }
        });
    }

    /**
     * Restore the old role values based on Spatie roles (for migration reversal)
     */
    private function restoreOldRoleValues(): void
    {
        // Define reverse mapping from Spatie roles to old role values
        $reverseMapping = [
            'Administrator' => 'admin',
            'Super Admin' => 'superadmin',
            'Student' => 'student',
            'Staff' => 'staff',
            'Lecturer' => 'lecturer',
            'Finance Manager' => 'finance',
            'Procurement Officer' => 'procurement',
            'Security Officer' => 'security',
            'Librarian' => 'librarian',
            'Store Manager' => 'store',
            'Parent' => 'parent',
            'Academic Officer' => 'academic',
            'IT Manager' => 'it',
            'System' => 'system',
        ];
        
        // Process each user
        User::query()->chunk(100, function ($users) use ($reverseMapping) {
            foreach ($users as $user) {
                $roles = $user->getRoleNames();
                if (count($roles) > 0) {
                    // Just use the first role if multiple exist
                    $spatieRole = $roles[0];
                    $user->role = $reverseMapping[$spatieRole] ?? 'user';
                    $user->save();
                } else {
                    // Default to user if no roles assigned
                    $user->role = 'user';
                    $user->save();
                }
            }
        });
    }
};
