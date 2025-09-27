<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PasswordSyncWebhookController extends Controller
{
    /**
     * Notify CIS when a user's password changes.
     *
     * @param User $user
     * @param string $plainPassword
     * @param string $event
     * @return bool
     */
    public function syncPasswordToCIS(User $user, string $plainPassword, string $event = 'password_changed'): bool
    {
        // Get CIS configuration
        $cisUrl = env('CIS_PASSWORD_SYNC_URL', 'https://college.pnmtc.edu.gh/api/auth-sync/password');
        $apiKey = env('CIS_PASSWORD_SYNC_API_KEY');
        
        if (!$cisUrl || !$apiKey) {
            Log::warning('CIS password sync not configured', [
                'user_email' => $user->email,
                'event' => $event,
            ]);
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($cisUrl, [
                    'email' => $user->email,
                    'password' => $plainPassword,
                    'event' => $event,
                    'api_key' => $apiKey,
                ]);

            if ($response->successful()) {
                Log::info('Password synchronized to CIS successfully', [
                    'user_email' => $user->email,
                    'event' => $event,
                    'cis_response' => $response->json(),
                ]);
                return true;
            } else {
                Log::error('Failed to sync password to CIS', [
                    'user_email' => $user->email,
                    'event' => $event,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception during CIS password sync', [
                'user_email' => $user->email,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sync password for multiple events (can be called from different parts of the app).
     *
     * @param string $email
     * @param string $plainPassword
     * @param string $event
     * @return bool
     */
    public static function syncUserPassword(string $email, string $plainPassword, string $event = 'password_changed'): bool
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            Log::warning('Attempted to sync password for non-existent user', [
                'email' => $email,
                'event' => $event,
            ]);
            return false;
        }
        
        $controller = new self();
        return $controller->syncPasswordToCIS($user, $plainPassword, $event);
    }
}