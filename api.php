<?php
// ─────────────────────────────────────────────
// Nur-Sim PHP Backend — api.php (Router)
// ─────────────────────────────────────────────

declare(strict_types=1);

// Disable error output to prevent HTML in JSON responses
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Log errors instead of displaying them
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/backend/cases.php';
require_once __DIR__ . '/backend/ai.php';
require_once __DIR__ . '/backend/email.php';
require_once __DIR__ . '/backend/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = rtrim($path, '/');

// Debug logging
error_log("Original REQUEST_URI: " . $_SERVER['REQUEST_URI']);
error_log("Original path: $path");

// More robust path parsing
if (preg_match('#^/Nur-Sim/PhP/api\.php(/.*)?$#', $path, $matches)) {
    $path = $matches[1] ?? '/';
    error_log("Matched Nur-Sim/PhP/api.php pattern, extracted path: $path");
} elseif (preg_match('#^/api\.php(/.*)?$#', $path, $matches)) {
    $path = $matches[1] ?? '/';
    error_log("Matched api.php pattern, extracted path: $path");
} elseif (preg_match('#^/api(/.*)?$#', $path, $matches)) {
    $path = $matches[1] ?? '/';
    error_log("Matched /api pattern, extracted path: $path");
}

error_log("Final path: $path");

// ── Route dispatch ────────────────────────────

try {
    // Debug logging
    error_log("API Request: $method $path");
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    error_log("Parsed path: $path");

    if ($method === 'GET' && $path === '/') {
        echo json_encode(['service' => 'Nur-Sim PHP API', 'version' => '1.0.0', 'status' => 'running']);

    } elseif ($method === 'GET' && $path === '/test') {
        echo json_encode(['message' => 'API test endpoint working', 'timestamp' => time()]);

    } elseif ($method === 'GET' && $path === '/test-db') {
        try {
            $db = get_db();
            $stmt = $db->query('SELECT COUNT(*) as user_count FROM users');
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['message' => 'Database connection successful', 'user_count' => $result['user_count']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }

    } elseif ($method === 'POST' && $path === '/auth/register') {
        route_register();

    } elseif ($method === 'POST' && $path === '/auth/login') {
        route_login();

    } elseif ($method === 'POST' && $path === '/auth/verify-otp') {
        route_verify_otp();

    } elseif ($method === 'POST' && $path === '/auth/resend-otp') {
        route_resend_otp();

    } elseif ($method === 'POST' && $path === '/auth/logout') {
        route_logout();

    } elseif ($method === 'GET' && $path === '/auth/me') {
        route_get_current_user();

    } elseif ($method === 'GET' && $path === '/user/sessions') {
        route_get_user_sessions();

    } elseif ($method === 'GET' && $path === '/cases') {
        route_list_cases();

    } elseif ($method === 'POST' && $path === '/sessions/start') {
        route_start_session();

    } elseif ($method === 'POST' && $path === '/chat') {
        route_chat();

    } elseif ($method === 'POST' && $path === '/sessions/end') {
        route_end_session();

    } elseif ($method === 'GET' && preg_match('#^/sessions/([a-f0-9\-]+)$#', $path, $m)) {
        route_get_session($m[1]);

    } elseif ($method === 'GET' && preg_match('#^/uploads/(.+)$#', $path, $m)) {
        $file_path = __DIR__ . '/backend/uploads/' . $m[1];
        if (file_exists($file_path)) {
            $mime_type = mime_content_type($file_path);
            header('Content-Type: ' . $mime_type);
            readfile($file_path);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'File not found']);
        }

    } elseif ($method === 'POST' && $path === '/admin/add-case') {
        route_add_case();

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    error_log("API Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'debug_path' => $path,
        'debug_method' => $method
    ]);
}