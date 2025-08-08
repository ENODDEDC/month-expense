<?php
// Simple test script to check Brevo API
require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['BREVO_API_KEY'] ?? null;

if (!$apiKey) {
    echo "❌ BREVO_API_KEY not found in .env file\n";
    exit(1);
}

echo "✅ BREVO_API_KEY found: " . substr($apiKey, 0, 10) . "...\n";

// Test API connection
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.sendinblue.com/v3/account');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'api-key: ' . $apiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    echo "✅ Brevo API connection successful!\n";
} elseif ($httpCode === 401) {
    $data = json_decode($response, true);
    if (isset($data['message']) && strpos($data['message'], 'unrecognised IP') !== false) {
        echo "❌ IP address not authorized in Brevo\n";
        echo "Go to: https://app.brevo.com/security/authorised_ips\n";
    } else {
        echo "❌ Invalid API key\n";
    }
} else {
    echo "❌ API connection failed\n";
}