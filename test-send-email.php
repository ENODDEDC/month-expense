<?php
// Test script to actually send an email via Brevo
require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['BREVO_API_KEY'] ?? null;

if (!$apiKey) {
    echo "❌ BREVO_API_KEY not found\n";
    exit(1);
}

echo "Testing email send...\n";

$testEmail = 'enodd.coding.20@gmail.com'; // Using your own email for testing
$testOtp = '123456';

$data = [
    'sender' => [
        'name' => 'ENODD',
        'email' => 'enodd.coding.20@gmail.com',
    ],
    'to' => [
        ['email' => $testEmail, 'name' => 'Test User']
    ],
    'subject' => 'Test OTP Email',
    'htmlContent' => '<html><body><h1>Test OTP: ' . $testOtp . '</h1><p>This is a test email from your Laravel app.</p></body></html>'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/smtp/email');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'api-key: ' . $apiKey,
    'content-type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 201) {
    echo "✅ Email sent successfully!\n";
    echo "Check your inbox: $testEmail\n";
} else {
    echo "❌ Email sending failed\n";
    $responseData = json_decode($response, true);
    if (isset($responseData['message'])) {
        echo "Error: " . $responseData['message'] . "\n";
    }
}