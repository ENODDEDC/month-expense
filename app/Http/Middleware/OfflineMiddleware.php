<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OfflineMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Let the dashboard handle offline states naturally
        // Only intervene if there are critical database errors
        if ($this->isDashboardRequest($request)) {
            try {
                return $next($request);
            } catch (\Exception $e) {
                // Only handle severe database connection errors
                if ($this->isCriticalDatabaseError($e)) {
                    return $this->serveOfflinePage();
                }
                throw $e;
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if the exception is a critical database connection error
     */
    private function isCriticalDatabaseError(\Exception $e): bool
    {
        return str_contains($e->getMessage(), 'could not translate host name') ||
               str_contains($e->getMessage(), 'Connection refused') ||
               str_contains($e->getMessage(), 'SQLSTATE') ||
               str_contains($e->getMessage(), 'database') ||
               str_contains($e->getMessage(), 'connection');
    }
    
    /**
     * Check if this is a dashboard request
     */
    private function isDashboardRequest(Request $request): bool
    {
        return $request->is('dashboard') || $request->is('/');
    }
    
    /**
     * Serve the offline page
     */
    private function serveOfflinePage(): Response
    {
        // Since we removed offline.html, serve the dashboard with offline context
        // The dashboard will handle offline state through JavaScript
        try {
            // Try to get user expenses if possible, otherwise use empty collection
            $expenses = collect();
            if (auth()->check()) {
                try {
                    $expenses = auth()->user()->expenses;
                } catch (\Exception $e) {
                    // If we can't get expenses due to DB issues, use empty collection
                    $expenses = collect();
                }
            }
            
            return response()->view('dashboard', [
                'expenses' => $expenses,
                'offline' => true
            ]);
        } catch (\Exception $e) {
            // Ultimate fallback - basic offline page
            return response($this->getBasicOfflinePage(), 200, ['Content-Type' => 'text/html']);
        }
    }
    
    /**
     * Get a basic offline page as fallback
     */
    private function getBasicOfflinePage(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Expense Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f3f4f6; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .icon { font-size: 64px; margin-bottom: 20px; }
        h1 { color: #374151; margin-bottom: 10px; }
        p { color: #6b7280; margin-bottom: 20px; }
        .btn { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📱</div>
        <h1>You\'re Offline</h1>
        <p>The expense tracker is currently offline. Please check your internet connection and try again.</p>
        <p>You can still use the app offline - any expenses you add will sync when you\'re back online.</p>
        <button onclick="window.location.reload()" class="btn">Try Again</button>
    </div>
</body>
</html>';
    }
}
