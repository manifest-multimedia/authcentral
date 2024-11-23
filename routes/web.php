<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::get('/user-management', function () {
    return view('backend.users');
})->name('user.management');


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
        return redirect('/dashboard');
    }
    return view('login');
})->name('login');




Route::get('/sign-up', function () {
    return view('register');
})->name('sign-up');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',


])->group(function () {
    Route::get('/portal', function () {
        return view('backend.dashboard');
    })->name('dashboard');

    Route::get('/college-portal', function () {
        return redirect()->away('https://college.pnmtc.edu.gh');
    })->name('college.portal');
});


include_once __DIR__ . '/fortify.php';
