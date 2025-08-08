<?php
// Test Laravel HTTP client with Brevo
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = env('BREVO_API_KEY');

if (!$apiKey) {
    echo "❌ BREVO_API_KEY not found\n";
    exit(1);
}

echo "Testing Laravel HTTP client with Brevo...\n";
echo "API Key: " . substr($apiKey, 0, 10) . "...\n";

$testEmail = 'enodd.coding.20@gmail.com';
$testOtp = '123456';

try {
    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'api-key' => $apiKey,
        'Content-Type' => 'application/json',
    ])->post('https://api.sendinblue.com/v3/smtp/email', [
        'sender' => [
            'name' => 'ENODD',
            'email' => 'enodd.coding.20@gmail.com',
        ],
        'to' => [['email' => $testEmail, 'name' => 'Test User']],
        'subject' => 'Laravel HTTP Test OTP',
        'htmlContent' => '<html><body><h1>Test OTP: ' . $testOtp . '</h1><p>This is a test from Laravel HTTP client.</p></body></html>'
    ]);

    echo "HTTP Status: " . $response->status() . "\n";
    echo "Response: " . $response->body() . "\n";

    if ($response->successful()) {
        echo "✅ Laravel HTTP client email sent successfully!\n";
    } else {
        echo "❌ Laravel HTTP client email failed\n";
    }
} catch (\Throwable $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}