<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;

// Public Endpoints
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Admin Setup Endpoints
Route::get('/admin-exists', [AuthController::class, 'adminExists']);
Route::post('/admin-setup', [AuthController::class, 'setupAdmin']);

// Public Poll endpoints
Route::get('/active-polls', [PollController::class, 'active']);
Route::get('/polls/{poll}', [PollController::class, 'show']);
Route::post('/polls/{poll}/vote', [VoteController::class, 'store']);

// Protected Admin Endpoints
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Admin Poll Management
    Route::get('/polls', [PollController::class, 'index']);
    Route::post('/polls', [PollController::class, 'store']);
    Route::put('/polls/{poll}', [PollController::class, 'update']);
    Route::delete('/polls/{poll}', [PollController::class, 'destroy']);
    Route::get('/polls/{poll}/admin', [PollController::class, 'adminShow']);
    Route::patch('/polls/{poll}/toggle-status', [PollController::class, 'toggleStatus']);
});
