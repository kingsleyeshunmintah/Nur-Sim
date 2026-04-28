<?php
// Redirect to the front page
header('Location: front.php');
exit;
?>

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = rtrim($path, '/');

// Strip the subdirectory prefix if present
$prefix = '/Nur-Sim/PhP';
if (strpos($path, $prefix) === 0) {
    $path = substr($path, strlen($prefix));
    $path = $path ?: '/';  // If empty, it's the root
}

// ── Route dispatch ────────────────────────────

try {
    if ($method === 'GET' && $path === '/') {
        echo json_encode(['service' => 'Nur-Sim PHP API', 'version' => '1.0.0', 'status' => 'running']);

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
            'id'           => $c['id'],
            'title'        => $c['title'],
            'patient_name' => $c['patient_name'],
            'age'          => $c['age'],
            'gender'       => $c['gender'],
            'diagnosis'    => $c['diagnosis'],
            'difficulty'   => $c['difficulty'],
            'tags'         => $c['tags'],
        ];
    }
    echo json_encode($out);
}

function route_start_session(): void {
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $case_id    = $body['case_id']    ?? '';
    $student_id = $body['student_id'] ?? 'anonymous';

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
        'INSERT INTO simulation_sessions (id, case_id, student_id, started_at) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$session_id, $case_id, $student_id, $now]);

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
        ],
        'started_at' => $now,
    ]);
}

function route_chat(): void {
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $session_id = $body['session_id'] ?? '';
    $message    = trim($body['message'] ?? '');

    if (!$session_id || !$message) {
        http_response_code(400);
        echo json_encode(['error' => 'session_id and message are required']);
        return;
    }

    $db  = get_db();
    $row = $db->query("SELECT * FROM simulation_sessions WHERE id = '$session_id'")->fetch(PDO::FETCH_ASSOC);

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

    // Call AI
    $reply = call_ai($case['system_prompt'], $messages);

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
    $body       = json_decode(file_get_contents('php://input'), true) ?? [];
    $session_id = $body['session_id'] ?? '';

    if (!$session_id) {
        http_response_code(400);
        echo json_encode(['error' => 'session_id is required']);
        return;
    }

    $db  = get_db();
    $row = $db->query("SELECT * FROM simulation_sessions WHERE id = '$session_id'")->fetch(PDO::FETCH_ASSOC);

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
    $db  = get_db();
    $row = $db->query("SELECT * FROM simulation_sessions WHERE id = '$session_id'")->fetch(PDO::FETCH_ASSOC);
    if (!$row) { http_response_code(404); echo json_encode(['error' => 'Not found']); return; }

    $logs = $db->query(
        "SELECT role, content, timestamp FROM performance_logs WHERE session_id = '$session_id' ORDER BY timestamp ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['session' => $row, 'messages' => $logs]);
}

function generate_uuid(): string {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
