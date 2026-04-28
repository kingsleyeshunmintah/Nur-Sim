<?php
// Test email functions
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/email.php';

echo "Testing email functions...\n\n";

$test_email = 'test@example.com';
$test_otp = generate_otp();

echo "Test OTP: $test_otp\n";
echo "Test Email: $test_email\n\n";

// Test send_otp_email function (this will try to send email)
echo "Testing send_otp_email function...\n";
$result = send_otp_email($test_email, $test_otp);
echo "Email send result: " . ($result ? "SUCCESS" : "FAILED") . "\n";

echo "\n✅ Email functions loaded successfully!\n";
echo "Note: Actual email sending depends on SMTP configuration.\n";
?>