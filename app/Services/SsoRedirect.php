<?php

namespace App\Services;

use App\Models\User;

class SsoRedirect
{
    public function validate(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        $parts = parse_url($url);
        abort_unless($parts && ($parts['scheme'] ?? '') === 'https' && ! isset($parts['user']) && ! isset($parts['pass'])
            && ! isset($parts['query']) && ! isset($parts['fragment']) && in_array($url, config('sso.allowed_callbacks', []), true), 422, 'This callback is not allowed.');

        return $url;
    }

    public function redirect(User $user, ?string $url)
    {
        $url = $this->validate($url);
        if (! $url) {
            return redirect('/portal');
        }
        $token = $user->createToken('college-sso', ['identity:read'], now()->addMinutes(5))->plainTextToken;

        return redirect()->away($url.'?'.http_build_query(['token' => $token]));
    }
}
