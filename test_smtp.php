<?php
// Test Gmail SMTP connection
require_once __DIR__ . '/backend/config.php';

echo "<h1>Gmail SMTP Connection Test</h1>\n";
echo "<pre>\n";

echo "SMTP Configuration:\n";
echo "Host: " . SMTP_HOST . "\n";
echo "Port: " . SMTP_PORT . "\n";
echo "Username: " . SMTP_USERNAME . "\n";
echo "Encryption: " . SMTP_ENCRYPTION . "\n\n";

echo "Testing basic SMTP connection...\n";

// Test basic socket connection
$host = SMTP_HOST;
$port = SMTP_PORT;
$timeout = 10;

echo "Attempting to connect to $host:$port...\n";

$socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

if (!$socket) {
    echo "❌ Connection failed: $errstr ($errno)\n";
    echo "\nPossible issues:\n";
    echo "- Firewall blocking outbound connections\n";
    echo "- Incorrect hostname or port\n";
    echo "- Network connectivity issues\n";
} else {
    echo "✅ Basic connection successful!\n";

    // Try to read server greeting
    $response = fgets($socket, 512);
    echo "Server response: " . trim($response) . "\n";

    fclose($socket);
    echo "✅ Socket connection test passed!\n";
}

echo "\nGmail SMTP Troubleshooting:\n";
echo "1. Make sure 2FA is enabled on your Gmail account\n";
echo "2. Generate an 'App Password' in Google Account settings\n";
echo "3. Use the App Password (not your regular password) in SMTP_PASSWORD\n";
echo "4. Check if your Gmail account has any security restrictions\n";
echo "5. Try using port 465 with SSL instead of 587 with TLS\n";

echo "</pre>\n";
?>