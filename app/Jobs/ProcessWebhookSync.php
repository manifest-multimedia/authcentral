<?php

namespace App\Jobs;

use App\Models\WebhookSyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessWebhookSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 300, 1800, 7200]; // 30s, 5m, 30m, 2h
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected WebhookSyncLog $webhookLog
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get app config
            $appConfig = config("auth.connected_apps.{$this->webhookLog->app_identifier}");
            if (!$appConfig || !isset($appConfig['webhook_url'])) {
                throw new \Exception("Invalid app configuration for {$this->webhookLog->app_identifier}");
            }

            // Send webhook
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $appConfig['api_key'],
                    'Content-Type' => 'application/json',
                ])
                ->post($appConfig['webhook_url'], $this->webhookLog->payload);

            if ($response->successful()) {
                // Update webhook log
                $this->webhookLog->update([
                    'status' => 'success',
                    'processed_at' => now(),
                    'error_message' => null,
                ]);

                Log::info('Webhook sync processed successfully', [
                    'webhook_id' => $this->webhookLog->id,
                    'app' => $this->webhookLog->app_identifier,
                    'event_type' => $this->webhookLog->event_type,
                ]);
            } else {
                throw new \Exception("Webhook failed with status {$response->status()}: {$response->body()}");
            }
        } catch (\Exception $e) {
            // Increment retry count
            $this->webhookLog->increment('retry_count');
            
            // Update error message
            $this->webhookLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('Webhook sync failed', [
                'webhook_id' => $this->webhookLog->id,
                'app' => $this->webhookLog->app_identifier,
                'event_type' => $this->webhookLog->event_type,
                'error' => $e->getMessage(),
                'retry_count' => $this->webhookLog->retry_count,
            ]);

            // If max retries reached, mark as permanently failed
            if ($this->webhookLog->retry_count >= $this->tries) {
                $this->webhookLog->update([
                    'status' => 'failed',
                    'error_message' => "Max retries ({$this->tries}) reached. Last error: {$e->getMessage()}",
                ]);

                Log::error('Webhook sync permanently failed', [
                    'webhook_id' => $this->webhookLog->id,
                    'app' => $this->webhookLog->app_identifier,
                ]);
            } else {
                // Otherwise, throw exception to trigger retry
                throw $e;
            }
        }
    }
}
