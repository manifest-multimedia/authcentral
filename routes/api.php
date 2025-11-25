<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    return [
        'user' => $user,
        'roles' => $user->roles->pluck('name')
    ];
});

// Activity log API endpoint
Route::post('/activity-log', [ActivityLogController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Roles & Permissions Registry API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('registry/roles-permissions')->group(function () {
    Route::post('/sync', [\App\Http\Controllers\Api\RolesPermissionsRegistryController::class, 'handleWebhook']);
    Route::get('/health', [\App\Http\Controllers\Api\RolesPermissionsRegistryController::class, 'healthCheck']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/{appIdentifier}', [\App\Http\Controllers\Api\RolesPermissionsRegistryController::class, 'getRegistry']);
        Route::post('/{appIdentifier}/reconcile', [\App\Http\Controllers\Api\RolesPermissionsRegistryController::class, 'triggerReconciliation']);
    });
});
