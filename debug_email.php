<?php
// Debug email sending
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/email.php';

echo "<h1>Email Debug Test</h1>\n";
echo "<pre>\n";

// Test basic email sending
$test_email = 'test@example.com';
$test_otp = generate_otp();

echo "Test OTP: $test_otp\n";
echo "Test Email: $test_email\n\n";

echo "SMTP Configuration:\n";
echo "Host: " . SMTP_HOST . "\n";
echo "Port: " . SMTP_PORT . "\n";
echo "Username: " . SMTP_USERNAME . "\n";
echo "Encryption: " . SMTP_ENCRYPTION . "\n";
echo "From: " . FROM_EMAIL . "\n\n";

echo "Testing send_otp_email function...\n";
$result = send_otp_email($test_email, $test_otp);

echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

if ($result['success']) {
    echo "✅ Email sent successfully!\n";
} else {
    echo "❌ Email sending failed!\n";
    echo "Error: " . $result['message'] . "\n";
    if (isset($result['error_info'])) {
        echo "SMTP Error Info: " . $result['error_info'] . "\n";
    }
}

echo "\nEmail Preview:\n";
echo $result['email_preview'];

echo "</pre>\n";
?>