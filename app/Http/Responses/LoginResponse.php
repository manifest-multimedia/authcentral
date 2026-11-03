<?php

namespace App\Http\Responses;

use App\Services\SsoRedirect;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        return app(SsoRedirect::class)->redirect(Auth::guard('web')->user(), $request->input('redirect_url') ?? $request->input('redirect_uri'));
    }
}
