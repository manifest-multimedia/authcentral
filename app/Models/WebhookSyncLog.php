<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class WebhookSyncLog extends Model
{
    protected $table = 'webhook_sync_logs';

    protected $fillable = [
        'app_identifier',
        'event_type',
        'payload',
        'status',
        'error_message',
        'retry_count',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Get pending webhooks that need to be retried
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending')
                    ->where('retry_count', '<', 5)
                    ->orderBy('created_at');
    }

    /**
     * Get failed webhooks
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                          ->where('retry_count', '>=', 5);
                    });
    }

    /**
     * Get successful webhooks
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to filter by app
     */
    public function scopeForApp($query, string $appIdentifier): Builder
    {
        return $query->where('app_identifier', $appIdentifier);
    }

    /**
     * Mark the webhook as processed
     */
    public function markProcessed(bool $success, ?string $error = null): bool
    {
        return $this->update([
            'status' => $success ? 'success' : 'failed',
            'error_message' => $error,
            'processed_at' => now(),
        ]);
    }

    /**
     * Increment the retry count
     */
    public function incrementRetry(): bool
    {
        return $this->update([
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * Check if webhook should be retried
     */
    public function shouldRetry(): bool
    {
        return $this->status === 'pending' && $this->retry_count < 5;
    }

    /**
     * Get retry delay in seconds based on retry count
     */
    public function getRetryDelay(): int
    {
        return match ($this->retry_count) {
            0 => 30,       // 30 seconds
            1 => 300,      // 5 minutes
            2 => 1800,     // 30 minutes
            3 => 7200,     // 2 hours
            default => 21600, // 6 hours
        };
    }
}
