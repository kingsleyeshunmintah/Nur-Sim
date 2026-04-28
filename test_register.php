<?php
// Test registration
$url = 'http://localhost/Nur-Sim/PhP/api.php/auth/register';
$data = [
    'email' => 'test@example.com',
    'password' => 'testpass123',
    'first_name' => 'Test',
    'last_name' => 'User'
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Registration Response:\n";
echo $result . "\n";
?>