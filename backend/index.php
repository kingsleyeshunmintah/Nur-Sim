<?php
// ─────────────────────────────────────────────
// Nur-Sim PHP Backend — index.php (Router)
// ─────────────────────────────────────────────

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/cases.php';
require_once __DIR__ . '/ai.php';
require_once __DIR__ . '/auth.php';

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

// Strip the subdirectory prefix if present
$prefix = '/Nur-Sim/PhP/backend';
if (strpos($path, $prefix) === 0) {
    $path = substr($path, strlen($prefix));
    $path = $path ?: '/';  // If empty, it's the root
}

// ── Route dispatch ────────────────────────────

try {
    if ($method === 'GET' && $path === '/') {
        echo json_encode(['service' => 'Nur-Sim PHP API', 'version' => '1.0.0', 'status' => 'running']);

    } elseif ($method === 'POST' && $path === '/auth/register') {
        route_register();

    } elseif ($method === 'POST' && $path === '/auth/login') {
        route_login();

    } elseif ($method === 'POST' && $path === '/auth/logout') {
        route_logout();

    } elseif ($method === 'GET' && $path === '/auth/me') {
        route_get_current_user();

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

    } elseif ($method === 'GET' && $path === '/user/sessions') {
        route_get_user_sessions();

    } elseif ($method === 'POST' && $path === '/admin/add-case') {
        route_add_case();

    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// ─────────────────────────────────────────────
// Route Handlers
// ─────────────────────────────────────────────

function route_list_cases(): void {
    $cases = get_cases();
    $out   = [];
    foreach ($cases as $c) {
        $out[] = [
            'id'            => $c['id'],
            'title'         => $c['title'],
            'patient_name'  => $c['patient_name'],
            'age'           => $c['age'],
            'gender'        => $c['gender'],
            'diagnosis'     => $c['diagnosis'],
            'difficulty'    => $c['difficulty'],
            'tags'          => $c['tags'],
            'profile_image' => $c['profile_image'],
            'bp'            => $c['bp'] ?? '',
            'hr'            => $c['hr'] ?? '',
            'rr'            => $c['rr'] ?? '',
            'temp'          => $c['temp'] ?? '',
            'spo2'          => $c['spo2'] ?? '',
        ];
    }
    echo json_encode($out);
}

function route_start_session(): void {
    $user = require_auth();
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $case_id    = $body['case_id']    ?? '';

    $cases = get_cases();
    if (!isset($cases[$case_id])) {
        http_response_code(404);
        echo json_encode(['error' => "Case '$case_id' not found"]);
        return;
    }

    $db         = get_db();
    $session_id = generate_uuid();
    $now        = date('c');
    $case       = $cases[$case_id];

    $stmt = $db->prepare(
        'INSERT INTO simulation_sessions (id, case_id, user_id, started_at) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$session_id, $case_id, $user['id'], $now]);

    echo json_encode([
        'session_id' => $session_id,
        'case'       => [
            'id'           => $case['id'],
            'title'        => $case['title'],
            'patient_name' => $case['patient_name'],
            'age'          => $case['age'],
            'gender'       => $case['gender'],
            'diagnosis'    => $case['diagnosis'],
            'difficulty'   => $case['difficulty'],
            'tags'         => $case['tags'],
            'bp'           => $case['bp'] ?? '',
            'hr'           => $case['hr'] ?? '',
            'rr'           => $case['rr'] ?? '',
            'temp'         => $case['temp'] ?? '',
            'spo2'         => $case['spo2'] ?? '',
        ],
        'started_at' => $now,
    ]);
}

function route_chat(): void {
    $user = require_auth();
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $session_id = $body['session_id'] ?? '';
    $message    = trim($body['message'] ?? '');

    if (!$session_id || !$message) {
        http_response_code(400);
        echo json_encode(['error' => 'session_id and message are required']);
        return;
    }

    $db  = get_db();
    $row = $db->query("SELECT * FROM simulation_sessions WHERE id = '$session_id' AND user_id = {$user['id']}")->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Session not found']);
        return;
    }
    if ($row['status'] !== 'active') {
        http_response_code(400);
        echo json_encode(['error' => 'Session has ended']);
        return;
    }

    $cases = get_cases();
    $case  = $cases[$row['case_id']] ?? null;
    if (!$case) {
        http_response_code(404);
        echo json_encode(['error' => 'Case not found']);
        return;
    }

    // Load history
    $logs     = $db->query(
        "SELECT role, content FROM performance_logs WHERE session_id = '$session_id' ORDER BY timestamp ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
    $messages = array_map(fn($l) => ['role' => $l['role'], 'content' => $l['content']], $logs);
    $messages[] = ['role' => 'user', 'content' => $message];

    // Build enhanced system prompt with current vitals
    $vitals_display = [];
    if (!empty($case['bp'])) $vitals_display[] = "BP: {$case['bp']}";
    if (!empty($case['hr'])) $vitals_display[] = "HR: {$case['hr']}";
    if (!empty($case['rr'])) $vitals_display[] = "RR: {$case['rr']}";
    if (!empty($case['temp'])) $vitals_display[] = "Temp: {$case['temp']}";
    if (!empty($case['spo2'])) $vitals_display[] = "SpO2: {$case['spo2']}";

    $vitals_context = !empty($vitals_display) 
        ? "\n\nCURRENT VITALS (as of this moment in the scenario):\n" . implode(", ", $vitals_display) . "\n\nBe sure to reference these vitals in your responses if medically relevant."
        : '';

    $enhanced_prompt = $case['system_prompt'] . $vitals_context;

    // Call AI
    $reply = call_ai($enhanced_prompt, $messages);

    // Persist
    $now = date('c');
    $ins = $db->prepare(
        'INSERT INTO performance_logs (id, session_id, timestamp, role, content) VALUES (?, ?, ?, ?, ?)'
    );
    $ins->execute([generate_uuid(), $session_id, $now, 'user',      $message]);
    $ins->execute([generate_uuid(), $session_id, $now, 'assistant', $reply]);

    $db->exec("UPDATE simulation_sessions SET message_count = message_count + 2 WHERE id = '$session_id'");

    echo json_encode(['reply' => $reply, 'session_id' => $session_id]);
}

function route_end_session(): void {
    $user = require_auth();
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $session_id = $body['session_id'] ?? '';

    if (!$session_id) {
        http_response_code(400);
        echo json_encode(['error' => 'session_id is required']);
        return;
    }

    $db  = get_db();
    $row = $db->query("SELECT * FROM simulation_sessions WHERE id = '$session_id' AND user_id = {$user['id']}")->fetch(PDO::FETCH_ASSOC);

    if (!$row) { http_response_code(404); echo json_encode(['error' => 'Session not found']); return; }
    if ($row['status'] !== 'active') { http_response_code(400); echo json_encode(['error' => 'Session already ended']); return; }

    $cases = get_cases();
    $case  = $cases[$row['case_id']];

    $logs     = $db->query(
        "SELECT role, content FROM performance_logs WHERE session_id = '$session_id' ORDER BY timestamp ASC"
    )->fetchAll(PDO::FETCH_ASSOC);
    $messages = array_map(fn($l) => ['role' => $l['role'], 'content' => $l['content']], $logs);

    $summary_system = "You are an experienced clinical nursing educator reviewing a student's simulation performance.

The student interacted with a patient named {$case['patient_name']} ({$case['age']} y/o, {$case['diagnosis']}).

Review the conversation and provide:
1. **Overall Performance Score** (0–100)
2. **Clinical Assessment** — Did the student assess systematically?
3. **Communication Skills** — Was the student empathetic, clear, and professional?
4. **Safety** — Were any unsafe practices observed?
5. **Key Strengths** (2–3 bullet points)
6. **Areas for Improvement** (2–3 bullet points)
7. **Recommended Learning Resources**

Be constructive, specific, and encouraging.";

    $summary_messages = [[
        'role'    => 'user',
        'content' => "Here is the full simulation transcript:\n\n" . json_encode($messages, JSON_PRETTY_PRINT) . "\n\nPlease provide a detailed performance review.",
    ]];

    $summary = call_ai($summary_system, $summary_messages);

    // Simple score parse
    $score = null;
    foreach (explode("\n", $summary) as $line) {
        if (stripos($line, 'score') !== false && preg_match('/\d{2,3}/', $line, $m)) {
            $score = min(100, (int)$m[0]);
            break;
        }
    }

    $now = date('c');
    $db->prepare(
        'INSERT OR REPLACE INTO session_summaries (id, session_id, summary, score, created_at) VALUES (?, ?, ?, ?, ?)'
    )->execute([generate_uuid(), $session_id, $summary, $score, $now]);

    $db->exec("UPDATE simulation_sessions SET status = 'ended', ended_at = '$now' WHERE id = '$session_id'");

    echo json_encode([
        'session_id'    => $session_id,
        'summary'       => $summary,
        'score'         => $score,
        'message_count' => (int)$row['message_count'],
        'ended_at'      => $now,
    ]);
}

function route_get_session(string $session_id): void {
    $user = require_auth();
    $db  = get_db();
    $row = $db->query("SELECT * FROM simulation_sessions WHERE id = '$session_id' AND user_id = {$user['id']}")->fetch(PDO::FETCH_ASSOC);
    if (!$row) { http_response_code(404); echo json_encode(['error' => 'Not found']); return; }

    $logs = $db->query(
        "SELECT role, content, timestamp FROM performance_logs WHERE session_id = '$session_id' ORDER BY timestamp ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['session' => $row, 'messages' => $logs]);
}

function route_add_case(): void {
    $user = require_auth();
    // TODO: Add admin role check here
    
    $data = $_POST;

    if (!$data || !isset($data['id'], $data['title'], $data['patient_name'], $data['age'], $data['gender'], $data['diagnosis'], $data['difficulty'], $data['tags'], $data['system_prompt'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid case data']);
        return;
    }

    $profile_image_path = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['profile_image']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
            $profile_image_path = 'uploads/' . $file_name;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload image']);
            return;
        }
    }

    $db = get_db();
    $stmt = $db->prepare("INSERT INTO cases (id, title, patient_name, age, gender, diagnosis, difficulty, tags, system_prompt, profile_image, bp, hr, rr, temp, spo2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([
            $data['id'],
            $data['title'],
            $data['patient_name'],
            $data['age'],
            $data['gender'],
            $data['diagnosis'],
            $data['difficulty'],
            json_encode(explode(',', $data['tags'])),  // assuming tags is comma separated
            $data['system_prompt'],
            $profile_image_path,
            $data['bp'] ?? '',
            $data['hr'] ?? '',
            $data['rr'] ?? '',
            $data['temp'] ?? '',
            $data['spo2'] ?? ''
        ]);
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
            http_response_code(409);
            echo json_encode(['error' => "A case with ID '{$data['id']}' already exists. Please use a different ID."]);
            return;
        }
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        return;
    }

    echo json_encode(['success' => true, 'message' => 'Case added']);
}

function generate_uuid(): string {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
