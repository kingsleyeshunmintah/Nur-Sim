<?php
require_once 'backend/config.php';
require_once 'backend/db.php';

try {
    $db = get_db();
    echo 'Database connection successful';
} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage();
}
?>