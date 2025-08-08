<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;

// Public Homepage for guests; redirect authenticated users to dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// OTP verification routes (CSRF excluded in middleware)
Route::get('/register/verify', [RegisterController::class, 'showVerifyOtp'])->name('register.verify.show');
Route::post('/register/verify', [RegisterController::class, 'verifyOtp'])->name('register.verify');
Route::post('/register/resend', [RegisterController::class, 'resendOtp'])->name('register.resend');

// Protected Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(['auth', \App\Http\Middleware\OfflineMiddleware::class]);
Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('auth');
Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('auth');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('auth');

// API Routes for PWA
Route::get('/api/expenses', [ExpenseController::class, 'index'])->name('api.expenses.index')->middleware('auth');

// Route to refresh CSRF token (no auth required for registration flow)
Route::get('/refresh-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});

// Test route to debug CSRF issues (remove after fixing)
Route::get('/test-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'app_key' => config('app.key') ? 'Set' : 'Missing'
    ]);
});

// Debug route to check recent OTP codes (only in debug mode)
Route::get('/debug/otp-logs', function () {
    if (!config('app.debug')) {
        abort(404);
    }
    
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return response()->json(['error' => 'Log file not found']);
    }
    
    $logs = file_get_contents($logFile);
    $otpLogs = [];
    
    // Extract OTP logs from the last 24 hours
    $lines = explode("\n", $logs);
    $cutoff = now()->subDay();
    
    foreach ($lines as $line) {
        if (strpos($line, 'OTP Generated') !== false) {
            // Parse the log line to extract timestamp and OTP info
            if (preg_match('/\[(.*?)\].*?"email":"(.*?)".*?"otp":"(.*?)"/', $line, $matches)) {
                $timestamp = $matches[1];
                $email = $matches[2];
                $otp = $matches[3];
                
                try {
                    $logTime = \Carbon\Carbon::parse($timestamp);
                    if ($logTime->greaterThan($cutoff)) {
                        $otpLogs[] = [
                            'timestamp' => $timestamp,
                            'email' => $email,
                            'otp' => $otp
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip invalid timestamps
                }
            }
        }
    }
    
    return response()->json([
        'recent_otps' => array_slice(array_reverse($otpLogs), 0, 10),
        'note' => 'Only available in debug mode. Shows last 10 OTP codes from past 24 hours.'
    ]);
})->name('debug.otp.logs');
