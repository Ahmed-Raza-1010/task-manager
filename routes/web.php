<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;

// Authentication Routes
Route::post('/send-otp', [AuthController::class, 'sendOtp']); // Send OTP
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Get Authenticated User
Route::middleware('auth:sanctum')->get('/api/user', function (Request $request) {
    return response()->json($request->user());
});

// Task API (Protected Routes)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']); 
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
});

// Catch-All Route for Vue SPA
Route::get('/{any}', function () {
    return view('app'); // Ensure resources/views/app.blade.php exists
})->where('any', '.*');
