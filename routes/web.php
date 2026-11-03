<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect(route('dashboard'));
    }

    return redirect('/login');
});

Route::get('/login', function (Request $request) {
    $redirectUrl = app(\App\Services\SsoRedirect::class)->validate($request->input('redirect_url') ?? $request->input('redirect_uri'));
    if (Auth::check()) {
        return app(\App\Services\SsoRedirect::class)->redirect(Auth::user(), $redirectUrl);
    }

    return view('login');
})->name('login');

Route::get('/register', fn () => view('register'))->name('register');

Route::get('/sign-up', function () {
    return view('register');
})->name('sign-up');

Route::get('account-reset', function () {
    return view('resetpass');
});

// Student Registration Routes
Route::get('/student/register', [StudentRegistrationController::class, 'create'])->name('student.register');
Route::post('/student/register', [StudentRegistrationController::class, 'store'])->name('student.register.store');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',

])->group(function () {
    Route::get('/portal', function () {
        // Redirect students to the college portal with token
        if (Auth::user()->isStudent()) {
            return app(\App\Services\SsoRedirect::class)->redirect(Auth::user(), config('sso.college_callback'));
        }

        return view('backend.dashboard');
    })->name('dashboard');

    Route::get('/college-portal', function () {
        return app(\App\Services\SsoRedirect::class)->redirect(Auth::user(), config('sso.college_callback'));
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
    Route::middleware(['role:System|Super Admin|Administrator'])->group(function () {
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

require __DIR__.'/fortify.php';
