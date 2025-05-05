<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Permission\Models\Role;

class AssignDefaultRoleOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 
     * When a user logs in, check if they have any roles assigned.
     * If not, assign them the 'Staff' role by default.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Check if the user has any roles assigned
        if ($user->roles->isEmpty()) {
            // Find or create the Staff role if it doesn't exist
            $staffRole = Role::findOrCreate('Staff');
            
            // Assign the Staff role to the user
            $user->assignRole($staffRole);
            
            // Log that a default role was assigned (optional)
            \Log::info("Default 'Staff' role assigned to user {$user->id} ({$user->email}) on login.");
        }
    }
}
