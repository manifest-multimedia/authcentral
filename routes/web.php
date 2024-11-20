<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
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
    Route::get('/dashboard', function () {
        return view('backend.dashboard');
    })->name('dashboard');
});


include_once __DIR__ . '/fortify.php';
