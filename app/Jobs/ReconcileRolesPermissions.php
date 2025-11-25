<?php

namespace App\Jobs;

use App\Models\RolePermissionRegistry;
use App\Models\RolePermissionAssignmentRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReconcileRolesPermissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $appIdentifier
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting roles/permissions reconciliation for {$this->appIdentifier}");

        try {
            // Get app config
            $appConfig = config("auth.connected_apps.{$this->appIdentifier}");
            if (!$appConfig || !isset($appConfig['api_url'])) {
                throw new \Exception("Invalid app configuration for {$this->appIdentifier}");
            }

            // Fetch current roles/permissions from the app
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $appConfig['api_key'],
                    'Accept' => 'application/json',
                ])
                ->get($appConfig['api_url'] . '/roles-permissions');

            if (!$response->successful()) {
                throw new \Exception("Failed to fetch data: {$response->status()} - {$response->body()}");
            }

            $appData = $response->json();
            
            // Start reconciliation
            $this->reconcileRoles($appData['roles'] ?? []);
            $this->reconcilePermissions($appData['permissions'] ?? []);
            $this->reconcileAssignments($appData['assignments'] ?? []);

            Log::info("Completed reconciliation for {$this->appIdentifier}");

        } catch (\Exception $e) {
            Log::error("Reconciliation failed for {$this->appIdentifier}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Reconcile roles with the remote app
     */
    protected function reconcileRoles(array $appRoles): void
    {
        // Get current registry roles
        $registryRoles = RolePermissionRegistry::forApp($this->appIdentifier)
            ->roles()
            ->get()
            ->keyBy('entity_id');

        // Process each role from the app
        foreach ($appRoles as $appRole) {
            $registryRole = $registryRoles->get($appRole['id']);

            if (!$registryRole) {
                // New role, add to registry
                RolePermissionRegistry::create([
                    'app_identifier' => $this->appIdentifier,
                    'entity_type' => 'role',
                    'entity_id' => $appRole['id'],
                    'entity_name' => $appRole['name'],
                    'guard_name' => $appRole['guard_name'] ?? 'web',
                    'metadata' => $appRole['metadata'] ?? [],
                    'last_synced_at' => now(),
                ]);
            } else {
                // Update existing role if needed
                if ($registryRole->entity_name !== $appRole['name'] ||
                    $registryRole->guard_name !== ($appRole['guard_name'] ?? 'web')) {
                    $registryRole->update([
                        'entity_name' => $appRole['name'],
                        'guard_name' => $appRole['guard_name'] ?? 'web',
                        'metadata' => $appRole['metadata'] ?? [],
                        'last_synced_at' => now(),
                    ]);
                }
            }
        }

        // Soft delete any roles that no longer exist in the app
        $appRoleIds = collect($appRoles)->pluck('id')->toArray();
        RolePermissionRegistry::forApp($this->appIdentifier)
            ->roles()
            ->whereNotIn('entity_id', $appRoleIds)
            ->delete();
    }

    /**
     * Reconcile permissions with the remote app
     */
    protected function reconcilePermissions(array $appPermissions): void
    {
        // Get current registry permissions
        $registryPermissions = RolePermissionRegistry::forApp($this->appIdentifier)
            ->permissions()
            ->get()
            ->keyBy('entity_id');

        // Process each permission from the app
        foreach ($appPermissions as $appPermission) {
            $registryPermission = $registryPermissions->get($appPermission['id']);

            if (!$registryPermission) {
                // New permission, add to registry
                RolePermissionRegistry::create([
                    'app_identifier' => $this->appIdentifier,
                    'entity_type' => 'permission',
                    'entity_id' => $appPermission['id'],
                    'entity_name' => $appPermission['name'],
                    'guard_name' => $appPermission['guard_name'] ?? 'web',
                    'metadata' => $appPermission['metadata'] ?? [],
                    'last_synced_at' => now(),
                ]);
            } else {
                // Update existing permission if needed
                if ($registryPermission->entity_name !== $appPermission['name'] ||
                    $registryPermission->guard_name !== ($appPermission['guard_name'] ?? 'web')) {
                    $registryPermission->update([
                        'entity_name' => $appPermission['name'],
                        'guard_name' => $appPermission['guard_name'] ?? 'web',
                        'metadata' => $appPermission['metadata'] ?? [],
                        'last_synced_at' => now(),
                    ]);
                }
            }
        }

        // Soft delete any permissions that no longer exist in the app
        $appPermissionIds = collect($appPermissions)->pluck('id')->toArray();
        RolePermissionRegistry::forApp($this->appIdentifier)
            ->permissions()
            ->whereNotIn('entity_id', $appPermissionIds)
            ->delete();
    }

    /**
     * Reconcile role-permission assignments with the remote app
     */
    protected function reconcileAssignments(array $appAssignments): void
    {
        // Get current registry assignments
        $registryAssignments = RolePermissionAssignmentRegistry::forApp($this->appIdentifier)
            ->get()
            ->keyBy(function ($item) {
                return "{$item->role_id}_{$item->permission_id}";
            });

        // Process each assignment from the app
        foreach ($appAssignments as $appAssignment) {
            $key = "{$appAssignment['role_id']}_{$appAssignment['permission_id']}";
            $registryAssignment = $registryAssignments->get($key);

            if (!$registryAssignment) {
                // New assignment, add to registry
                RolePermissionAssignmentRegistry::create([
                    'app_identifier' => $this->appIdentifier,
                    'role_id' => $appAssignment['role_id'],
                    'role_name' => $appAssignment['role_name'],
                    'permission_id' => $appAssignment['permission_id'],
                    'permission_name' => $appAssignment['permission_name'],
                    'synced_at' => now(),
                ]);
            }
        }

        // Soft delete any assignments that no longer exist in the app
        $appAssignmentKeys = collect($appAssignments)->map(function ($assignment) {
            return "{$assignment['role_id']}_{$assignment['permission_id']}";
        })->toArray();

        RolePermissionAssignmentRegistry::forApp($this->appIdentifier)
            ->whereNotIn(
                DB::raw("CONCAT(role_id, '_', permission_id)"),
                $appAssignmentKeys
            )
            ->delete();
    }
}
