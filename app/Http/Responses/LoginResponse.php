<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {

        // Retrieve the redirect URL if it exists
        $redirectUrl = $this->parceRedirectUrl($request->input('redirect_url') ?? $request->input('redirect_uri'));

        $user = Auth::guard('web')->user();

        // Generate a token for the user
        $token = $user->createToken('auth-token')->plainTextToken;


        if ($redirectUrl) {
            // Append the token to the redirect URL as a query parameter
            return redirect()->away($redirectUrl . '?token=' . $token);
        }

        // Fallback if no redirect URL is provided
        return redirect('/');
    }

    private function parceRedirectUrl($url)
    {
        if ($url) {
            return $url;
        } else {
            return url('/');
        }
    }
}
