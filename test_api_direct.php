<?php
// Test script to directly call API endpoints
echo "Testing API endpoints...\n\n";

// Test root endpoint
echo "1. Testing root endpoint (/):\n";
$result = file_get_contents('http://localhost/Nur-Sim/PhP/api.php/');
echo $result . "\n\n";

// Test test endpoint
echo "2. Testing test endpoint (/test):\n";
$result = file_get_contents('http://localhost/Nur-Sim/PhP/api.php/test');
echo $result . "\n\n";

// Test database endpoint
echo "3. Testing database endpoint (/test-db):\n";
$result = file_get_contents('http://localhost/Nur-Sim/PhP/api.php/test-db');
echo $result . "\n\n";

// Test auth register endpoint (POST)
echo "4. Testing auth register endpoint (POST):\n";
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode([
            'email' => 'test@example.com',
            'password' => 'testpass123'
        ])
    ]
]);
$result = file_get_contents('http://localhost/Nur-Sim/PhP/api.php/auth/register', false, $context);
echo $result . "\n\n";

echo "Test completed.\n";
?>