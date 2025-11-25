<?php

namespace App\Console\Commands;

use App\Jobs\RetryWebhookSync;
use App\Models\WebhookSyncLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPendingWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhooks:process-pending
                          {--limit=50 : Maximum number of webhooks to process}
                          {--app= : Process webhooks for a specific app only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending webhook synchronizations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $appIdentifier = $this->option('app');

        $this->info('Processing pending webhooks...');

        // Query for pending webhooks
        $query = WebhookSyncLog::pending();
        
        if ($appIdentifier) {
            $query->forApp($appIdentifier);
            $this->info("Filtering for app: {$appIdentifier}");
        }

        $pendingWebhooks = $query->take($limit)->get();
        $count = $pendingWebhooks->count();

        if ($count === 0) {
            $this->info('No pending webhooks found.');
            return;
        }

        $this->info("Found {$count} pending webhooks to process.");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $processed = 0;
        $failed = 0;

        foreach ($pendingWebhooks as $webhook) {
            try {
                // Calculate next retry delay
                $delay = $webhook->getRetryDelay();

                // Dispatch retry job
                RetryWebhookSync::dispatch($webhook)
                    ->delay(now()->addSeconds($delay))
                    ->onQueue('webhooks');

                $processed++;

            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to schedule webhook retry', [
                    'webhook_id' => $webhook->id,
                    'error' => $e->getMessage(),
                ]);
                
                $this->error("Failed to schedule webhook {$webhook->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Processed {$processed} webhooks.");
        if ($failed > 0) {
            $this->warn("Failed to process {$failed} webhooks.");
        }

        // Report on remaining pending webhooks
        $remaining = WebhookSyncLog::pending()->count();
        if ($remaining > 0) {
            $this->info("There are still {$remaining} webhooks pending processing.");
        }
    }
}
