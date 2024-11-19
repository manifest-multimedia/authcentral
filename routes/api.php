<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    if ($request->user()) {
        // Return user data if authenticated
        return response()->json([
            'email' => $request->user()->email,
            'name' => $request->user()->name,
            'role' => $request->user()->role ?? 'user',
        ]);
    }

    // Return error if user is not authenticated
    return response()->json(['error' => 'Unauthorized'], 401);
})->middleware('auth:sanctum');
