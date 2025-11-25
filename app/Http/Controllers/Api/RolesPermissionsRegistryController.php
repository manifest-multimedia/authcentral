<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RolePermissionRegistry;
use App\Models\RolePermissionAssignmentRegistry;
use App\Models\WebhookSyncLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RolesPermissionsRegistryController extends Controller
{
    /**
     * Handle incoming webhook for role/permission changes
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Validate API key
            $apiKey = config('auth.registry_api_key') ?: env('AUTHCENTRAL_REGISTRY_API_KEY');
            if (!$apiKey || !hash_equals($apiKey, $request->header('X-API-Key', ''))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Log webhook receipt
            $log = WebhookSyncLog::create([
                'app_identifier' => $request->input('app_identifier'),
                'event_type' => $request->input('event_type'),
                'payload' => $request->all(),
                'status' => 'pending',
            ]);

            // Validate request
            $validator = Validator::make($request->all(), [
                'event_type' => ['required', 'string', 'regex:/^(role|permission)\.(created|updated|deleted|permission\.(attached|detached))$/'],
                'app_identifier' => 'required|string|max:50',
                'timestamp' => 'required|date',
                'data' => 'required|array',
                'data.id' => 'required|integer',
                'data.name' => 'required|string',
                'data.guard_name' => 'nullable|string',
                'data.metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $log->markProcessed(false, 'Validation failed: ' . json_encode($validator->errors()));
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            try {
                $data = $request->input('data');
                $eventType = $request->input('event_type');
                $appId = $request->input('app_identifier');

                // Handle role/permission changes
                if (str_starts_with($eventType, 'role.') || str_starts_with($eventType, 'permission.')) {
                    $entityType = explode('.', $eventType)[0];
                    $action = explode('.', $eventType)[1];

                    if ($action === 'deleted') {
                        // Soft delete the entry
                        RolePermissionRegistry::where('app_identifier', $appId)
                            ->where('entity_type', $entityType)
                            ->where('entity_id', $data['id'])
                            ->delete();
                    } else {
                        // Create or update
                        RolePermissionRegistry::updateOrCreate(
                            [
                                'app_identifier' => $appId,
                                'entity_type' => $entityType,
                                'entity_id' => $data['id'],
                            ],
                            [
                                'entity_name' => $data['name'],
                                'guard_name' => $data['guard_name'] ?? 'web',
                                'metadata' => $data['metadata'] ?? null,
                                'last_synced_at' => now(),
                            ]
                        );
                    }
                }
                // Handle permission assignments
                elseif ($eventType === 'role.permission.attached' || $eventType === 'role.permission.detached') {
                    if ($eventType === 'role.permission.detached') {
                        // Soft delete the assignment
                        RolePermissionAssignmentRegistry::where('app_identifier', $appId)
                            ->where('role_id', $data['role_id'])
                            ->where('permission_id', $data['permission_id'])
                            ->delete();
                    } else {
                        // Create assignment
                        RolePermissionAssignmentRegistry::updateOrCreate(
                            [
                                'app_identifier' => $appId,
                                'role_id' => $data['role_id'],
                                'permission_id' => $data['permission_id'],
                            ],
                            [
                                'role_name' => $data['role_name'],
                                'permission_name' => $data['permission_name'],
                                'synced_at' => now(),
                            ]
                        );
                    }
                }

                DB::commit();
                $log->markProcessed(true);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook processed successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to process webhook', [
                    'error' => $e->getMessage(),
                    'payload' => $request->all()
                ]);
                
                $log->markProcessed(false, $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Internal server error'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Exception in webhook handler', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get registry entries for an app
     */
    public function getRegistry(Request $request, string $appIdentifier): JsonResponse
    {
        try {
            $roles = RolePermissionRegistry::forApp($appIdentifier)
                ->roles()
                ->with('assignments')
                ->get();

            $permissions = RolePermissionRegistry::forApp($appIdentifier)
                ->permissions()
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'roles' => $roles,
                    'permissions' => $permissions
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get registry', [
                'app' => $appIdentifier,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get registry'
            ], 500);
        }
    }

    /**
     * Health check endpoint
     */
    public function healthCheck(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'Roles & Permissions Registry',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Trigger manual reconciliation for an app
     */
    public function triggerReconciliation(string $appIdentifier): JsonResponse
    {
        // TODO: Implement reconciliation logic
        // This will be added when we implement the reconciliation feature
        return response()->json([
            'success' => true,
            'message' => 'Reconciliation triggered'
        ]);
    }
}
