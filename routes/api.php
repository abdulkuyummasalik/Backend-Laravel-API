<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Handle preflight request biar nggak error di frontend
Route::options('/{any}', function () {
    return response()->json(null, 200);
})->where('any', '.*');

// Login user
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () { // Rute di sini butuh login

    // Logout & refresh token
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Export user ke file
    Route::get('/users/export', [UserController::class, 'export']);

    // CRUD User
    Route::get('/users', [UserController::class, 'index']); // Ambil semua user
    Route::post('/users', [UserController::class, 'store']); // Tambah user
    Route::get('/users/{user}', [UserController::class, 'show']); // Detail user
    Route::put('/users/{user}', [UserController::class, 'update']); // Update user
    Route::delete('/users/{user}', [UserController::class, 'destroy']); // Hapus user

    // User yang udah dihapus (soft delete)
    Route::get('/users/deleted', [UserController::class, 'deleted']); // Lihat daftar user yang dihapus
    Route::post('/users/{id}/restore', [UserController::class, 'restore']); // Balikin user yang dihapus

    // Import user dari file
    Route::post('/users/import', [UserController::class, 'import']);
});
