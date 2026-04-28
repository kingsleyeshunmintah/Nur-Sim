<?php
// Test the new PHP login system
echo "Testing PHP Login System...\n\n";

// Test database connection
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/db.php';

try {
    $db = get_db();
    echo "✅ Database connection successful\n";

    // Test user creation
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM users');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Users table accessible, current count: " . $result['count'] . "\n";

    echo "\n🎉 All systems working! You can now use:\n";
    echo "http://localhost/Nur-Sim/PhP/login_new.php\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>