<?php

namespace App\Jobs;

use App\Models\WebhookSyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetryWebhookSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1; // We handle retries manually through WebhookSyncLog

    /**
     * The webhook sync log entry.
     *
     * @var \App\Models\WebhookSyncLog
     */
    protected $webhookLog;

    /**
     * Create a new job instance.
     */
    public function __construct(WebhookSyncLog $webhookLog)
    {
        $this->webhookLog = $webhookLog;
        $this->queue = 'webhooks';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->webhookLog->shouldRetry()) {
            Log::info('Webhook retry skipped - max retries reached or already processed', [
                'webhook_id' => $this->webhookLog->id,
                'retry_count' => $this->webhookLog->retry_count,
                'status' => $this->webhookLog->status,
            ]);
            return;
        }

        try {
            // Increment retry count
            $this->webhookLog->incrementRetry();

            // Re-submit the webhook to the handler
            $response = app(\App\Http\Controllers\Api\RolesPermissionsRegistryController::class)
                ->handleWebhook(new \Illuminate\Http\Request($this->webhookLog->payload));

            // Check if successful
            if ($response->getStatusCode() === 200 && json_decode($response->getContent())->success) {
                Log::info('Webhook retry succeeded', [
                    'webhook_id' => $this->webhookLog->id,
                    'retry_count' => $this->webhookLog->retry_count,
                ]);
                return;
            }

            throw new \Exception('Webhook retry failed: ' . $response->getContent());

        } catch (\Exception $e) {
            Log::error('Webhook retry failed', [
                'webhook_id' => $this->webhookLog->id,
                'retry_count' => $this->webhookLog->retry_count,
                'error' => $e->getMessage(),
            ]);

            // If we should retry again, schedule the next attempt
            if ($this->webhookLog->shouldRetry()) {
                $delay = $this->webhookLog->getRetryDelay();
                
                self::dispatch($this->webhookLog)
                    ->delay(now()->addSeconds($delay))
                    ->onQueue('webhooks');
            } else {
                // Mark as permanently failed
                $this->webhookLog->markProcessed(false, 'Max retries reached: ' . $e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Webhook retry job failed', [
            'webhook_id' => $this->webhookLog->id,
            'retry_count' => $this->webhookLog->retry_count,
            'error' => $exception->getMessage(),
        ]);
    }
}
