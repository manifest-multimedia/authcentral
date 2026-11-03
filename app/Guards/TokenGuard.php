<?php

namespace App\Guards;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TokenGuard
{
    public function authenticate(Request $request): ?array
    {
        $user = User::where('email', $request->input('email'))->first();
        // Fortify owns session creation, throttling and the 2FA challenge.
        // Checking credentials must not log in the user before that pipeline finishes.
        if (! $user || $user->status !== 'active' || ! Hash::check((string) $request->input('password'), $user->password)) {
            return null;
        }

        return ['user' => $user];
    }
}
