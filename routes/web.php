<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect(route('dashboard'));
    }
    return redirect('/login');
});


Route::get('/login', function (Request $request) {
    $redirectUrl = $request->input('redirect_url') ?? $request->input('redirect_uri');
    if (Auth::check()) {
        $user = Auth::user();
        // Generate a token for the user
        $token = $user->createToken('auth-token')->plainTextToken;


        if ($redirectUrl) {
            // Append the token to the redirect URL as a query parameter
            return redirect()->away($redirectUrl . '?token=' . $token);
        }
        return redirect(route('dashboard'));
    }
    return view('login');
})->name('login');




Route::get('/sign-up', function () {
    return view('register');
})->name('sign-up');

Route::get('account-reset', function () {
    return view('resetpass');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',


])->group(function () {
    Route::get('/portal', function () {
        return view('backend.dashboard');
    })->name('dashboard');

    Route::get('/college-portal', function () {
        $user = Auth::user();
        // Generate a token for the user
        $token = $user->createToken('auth-token')->plainTextToken;

        return redirect()->away('https://college.pnmtc.edu.gh/auth/callback?token=' . $token);
    })->name('college.portal');

    Route::get('/profile', function () {
        return view('backend.profile.index');
    })->name('account.profile');

    // Profile photo update route
    Route::put('/user/profile-photo', [ProfilePhotoController::class, 'update'])->name('user-profile-photo.update');

    // Activity logs route
    Route::get('/activities', [ActivityLogController::class, 'index'])->name('account.activity');

    Route::get('/2fa', function () {
        return view('backend.security.two-factor-auth');
    })->name('account.security');

    // User Management Routes - Only accessible by specific roles
    Route::middleware(['role:System|Super Admin|Administrator|Student'])->group(function () {
        // User Management
        Route::resource('users', UserController::class);
        
        // Role Management
        Route::resource('roles', RoleController::class);
        
        // Permission Management (restricted to System and Super Admin only)
        Route::middleware(['role:System|Super Admin'])->group(function () {
            Route::resource('permissions', PermissionController::class);
        });
    });
});

include_once __DIR__ . '/fortify.php';
