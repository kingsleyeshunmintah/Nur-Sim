<?php
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/db.php';

$db = get_db();
$stmt = $db->query('SELECT id, email, first_name, last_name, email_verified, email_verification_token FROM users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Current users in database:\n";
foreach ($users as $user) {
    echo "ID: {$user['id']}, Email: {$user['email']}, Name: {$user['first_name']} {$user['last_name']}, Verified: " . ($user['email_verified'] ? 'Yes' : 'No') . ", OTP: {$user['email_verification_token']}\n";
}
?>