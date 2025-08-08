<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: [
            '*',
        ]);
        
        // Add offline middleware only to specific routes (not auth routes)
        // Removed from web group to prevent interference with login
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Exception $e, $request) {
            // Check if this is a database connection error for dashboard requests
            if ($request->is('dashboard') || $request->is('/')) {
                if (str_contains($e->getMessage(), 'could not translate host name') ||
                    str_contains($e->getMessage(), 'Connection refused') ||
                    str_contains($e->getMessage(), 'SQLSTATE') ||
                    str_contains($e->getMessage(), 'database') ||
                    str_contains($e->getMessage(), 'connection')) {
                    
                    // Try to serve the dashboard with offline context
                    try {
                        // Get empty expenses collection for offline mode
                        $expenses = collect();
                        
                        // Try to render the dashboard view with offline flag
                        return response()->view('dashboard', [
                            'expenses' => $expenses,
                            'offline' => true
                        ]);
                    } catch (\Exception $viewException) {
                        // Ultimate fallback - basic offline page that redirects to dashboard
                        return response('<!DOCTYPE html>
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
    <script>
        // Auto-redirect to dashboard after 3 seconds
        setTimeout(function() {
            window.location.href = "/dashboard";
        }, 3000);
    </script>
</head>
<body>
    <div class="container">
        <div class="icon">📱</div>
        <h1>Loading Offline Mode</h1>
        <p>The expense tracker is starting in offline mode. You can still add expenses and they will sync when you are back online.</p>
        <a href="/dashboard" class="btn">Continue to Dashboard</a>
        <br><br>
        <p style="font-size: 12px; color: #9ca3af;">Redirecting automatically in 3 seconds...</p>
    </div>
</body>
</html>', 200, ['Content-Type' => 'text/html']);
                    }
                }
            }
        });
    })->create();
