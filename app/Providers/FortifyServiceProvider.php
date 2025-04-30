<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\LoginResponse;
use App\Guards\TokenGuard;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Http\Request; // Ensure Illuminate\Http\Request is imported
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Cache\RateLimiting\Limit;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Fortify::ignoreRoutes();
        // Bind the custom LoginResponse for Fortify
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    public function boot(): void
    {
        Fortify::loginView(function (Request $request) { // Use Illuminate\Http\Request here
            return view('login');
        });

        // Register the forgot password view
        Fortify::requestPasswordResetLinkView(function () {
            return view('forgot-password');
        });
        
        // Register the reset password view
        Fortify::resetPasswordView(function ($request) {
            return view('reset-password', ['request' => $request]);
        });

        Fortify::authenticateUsing(function (Request $request) { // Use Illuminate\Http\Request here
            // Use TokenGuard to handle authentication
            $result = (new TokenGuard())->authenticate($request);

            if ($result && isset($result['user'])) {
                return $result['user'];
            }

            return null;
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) { // Use Illuminate\Http\Request here
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) { // Use Illuminate\Http\Request here
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
