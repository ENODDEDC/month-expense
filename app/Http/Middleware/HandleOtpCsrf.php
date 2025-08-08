<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

class HandleOtpCsrf
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // If CSRF token mismatch occurs during OTP verification
            if ($request->routeIs('register.verify') || $request->routeIs('register.resend')) {
                // Regenerate the token and redirect back with error
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'csrf' => 'Security token expired. Please try submitting again.'
                ])->withInput();
            }
            
            // For other routes, let the default behavior handle it
            throw $e;
        }
    }
}