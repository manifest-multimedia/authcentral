<?php

namespace App\Guards;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class TokenGuard
{
    public function authenticate(Request $request)
    {

        // Get the credentials from the request
        $credentials = $request->only(['email', 'password']);

        // Throttle logic based on the username and the IP address
        $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

        // Check if too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json(['error' => 'Too many login attempts. Try again in ' . $seconds . ' seconds'], 429);
        }

        // Attempt to authenticate using the "web" guard
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            $token = $user->createToken('auth-token')->plainTextToken;

            // Clear the rate limiter after a successful login
            RateLimiter::clear($throttleKey);

            return ['user' => $user, 'token' => $token];
        }

        // Hit the rate limiter after a failed login attempt
        RateLimiter::hit($throttleKey);

        return null;
    }
}
