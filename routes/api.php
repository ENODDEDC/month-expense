<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

// OTP verification API routes (no CSRF protection)
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('api.register.verify');
Route::post('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('api.register.resend');