<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\WebhookSyncLog;
use App\Jobs\ProcessWebhookSync;
use App\Jobs\ReconcileRolesPermissions;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Retry failed webhooks every 5 minutes
Artisan::command('webhooks:retry', function () {
    $this->info('Processing failed webhook syncs...');
    
    $pendingWebhooks = WebhookSyncLog::where('status', 'failed')
        ->where('retry_count', '<', 5)
        ->where('created_at', '>=', now()->subHours(24))
        ->get();
        
    foreach ($pendingWebhooks as $webhook) {
        try {
            // Dispatch job to retry webhook
            ProcessWebhookSync::dispatch($webhook);
            $this->line("Queued retry for webhook {$webhook->id}");
        } catch (\Exception $e) {
            $this->error("Failed to queue webhook {$webhook->id}: {$e->getMessage()}");
        }
    }
    
    $this->info("Processed {$pendingWebhooks->count()} failed webhooks");
})->purpose('Retry failed webhook synchronizations')->everyFiveMinutes();

// Daily reconciliation of roles/permissions
Artisan::command('roles-permissions:reconcile', function() {
    $this->info('Starting roles and permissions reconciliation...');
    
    // Get list of connected apps from config
    $apps = config('connected_apps.apps', []);
    
    foreach ($apps as $appId => $appConfig) {
        try {
            ReconcileRolesPermissions::dispatch($appId);
            $this->line("Queued reconciliation for app: {$appId}");
        } catch (\Exception $e) {
            $this->error("Failed to queue reconciliation for {$appId}: {$e->getMessage()}");
        }
    }
    
    $this->info('Reconciliation jobs queued for all connected apps');
})->purpose('Reconcile roles and permissions with connected apps')->dailyAt('01:00');
