<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', \App\Http\Middleware\OfflineMiddleware::class]);
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('auth');
Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('auth');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('auth');

// API Routes for PWA
Route::get('/api/expenses', [ExpenseController::class, 'index'])->name('api.expenses.index')->middleware('auth');

// Route to refresh CSRF token
Route::get('/refresh-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->middleware('auth');

// Test route to debug CSRF issues (remove after fixing)
Route::get('/test-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'app_key' => config('app.key') ? 'Set' : 'Missing'
    ]);
});
