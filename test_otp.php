<?php
// Test OTP generation
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/email.php';

echo "Testing OTP generation...\n";
$otp = generate_otp();
echo "Generated OTP: $otp\n";
echo "OTP length: " . strlen($otp) . "\n";
echo "✅ OTP generation working!\n";
?>