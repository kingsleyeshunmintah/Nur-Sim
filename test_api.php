<?php
// Test script to check API responses
header('Content-Type: text/plain');

echo "Testing API endpoints...\n\n";

$base_url = 'http://localhost/Nur-Sim/PhP/api.php';

// Test basic endpoint
echo "1. Testing basic endpoint:\n";
echo "URL: $base_url/\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);
$result = file_get_contents("$base_url/", false, $context);
echo "Response: $result\n\n";

// Test database endpoint
echo "2. Testing database endpoint:\n";
echo "URL: $base_url/test-db\n";
$result = file_get_contents("$base_url/test-db", false, $context);
echo "Response: $result\n\n";

// Test auth register endpoint
echo "3. Testing auth register endpoint:\n";
echo "URL: $base_url/auth/register\n";
$data = json_encode([
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'first_name' => 'Test',
    'last_name' => 'User'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $data
    ]
]);
$result = file_get_contents("$base_url/auth/register", false, $context);
echo "Response: $result\n\n";

echo "Test completed.";
?>