<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class CollegeCredentialController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless($request->secure(), 400, 'HTTPS is required.');
        $key = config('college.service_key');
        abort_unless(is_string($key) && strlen($key) >= 32 && hash_equals($key, (string) $request->header('X-College-Service-Key')), 403);
        $data = $request->validate(['username' => ['required', 'string', 'max:191'], 'password' => ['required', 'string', 'max:4096']]);
        $username = trim($data['username']);
        $rateKey = 'college-credentials:'.hash('sha256', strtolower($username).'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            return response()->json(['message' => 'Try again later.'], 429);
        }
        RateLimiter::hit($rateKey, 60);
        $matches = User::where('email', $username)->orWhere('name', $username)->limit(2)->get();
        $user = $matches->count() === 1 ? $matches->first() : null;
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }
        if ($user->status !== 'active' || $user->two_factor_confirmed_at) {
            return response()->json(['message' => 'Additional account verification is required.'], 403);
        }
        RateLimiter::clear($rateKey);

        // Identity only: College360 owns roles and issues its own scoped canteen token.
        return response()->json(['authenticated' => true, 'user' => ['id' => $user->id, 'email' => $user->email, 'name' => $user->name]])
            ->header('Cache-Control', 'no-store, private');
    }
}
