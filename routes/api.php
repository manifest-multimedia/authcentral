<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::post('/login', function (Request $request) {
//     $credentials = $request->only('email', 'password');

//     if (Auth::attempt($credentials)) {
//         $user = Auth::user();
//         // Issue token for the user
//         $token = $user->createToken('auth-token')->plainTextToken;

//         return response()->json(['token' => $token, 'user' => $user], 200);
//     }

//     return response()->json(['error' => 'Unauthorized'], 401);
// });
