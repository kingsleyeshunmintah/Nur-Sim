<?php

function get_logged_in_user() {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    if (!preg_match('/Bearer (.+)/', $auth_header, $m)) {
        return null;
    }
    $token = $m[1];

    $db = get_db();
    $stmt = $db->prepare('SELECT u.id, u.email, u.first_name, u.last_name FROM user_sessions s JOIN users u ON s.user_id = u.id WHERE s.token = ? AND s.expires_at > datetime("now")');
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function require_auth() {
    $user = get_logged_in_user();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    return $user;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function generate_token() {
    return bin2hex(random_bytes(32));
}

function route_register() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['email']) || !isset($data['password']) || !isset($data['first_name']) || !isset($data['last_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email, password, first_name, and last_name are required']);
        return;
    }

    $email = trim($data['email']);
    $password = $data['password'];
    $first_name = trim($data['first_name']);
    $last_name = trim($data['last_name']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        return;
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'Password must be at least 6 characters']);
        return;
    }

    $db = get_db();
    $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already registered']);
        return;
    }

    // Generate OTP and send email
    $otp = generate_otp();
    $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    if (!send_otp_email($email, $otp)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to send verification email. Please try again.']);
        return;
    }

    $password_hash = hash_password($password);
    $stmt = $db->prepare('INSERT INTO users (email, password_hash, first_name, last_name, email_verification_token, email_verification_expires) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$email, $password_hash, $first_name, $last_name, $otp, $otp_expires]);

    echo json_encode([
        'message' => 'Registration successful! Please check your email for the verification code.',
        'requires_verification' => true
    ]);
}

function route_login() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['email']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }

    $email = trim($data['email']);
    $password = $data['password'];

    $db = get_db();
    $stmt = $db->prepare('SELECT id, email, password_hash, first_name, last_name, email_verified FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !verify_password($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid email or password']);
        return;
    }

    if (!$user['email_verified']) {
        // Send OTP for login verification
        $otp = generate_otp();
        $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        if (!send_login_otp_email($email, $otp)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send verification email. Please try again.']);
            return;
        }

        // Update OTP in database
        $stmt = $db->prepare('UPDATE users SET email_verification_token = ?, email_verification_expires = ? WHERE id = ?');
        $stmt->execute([$otp, $otp_expires, $user['id']]);

        echo json_encode([
            'message' => 'Please verify your email. Check your email for the verification code.',
            'requires_verification' => true,
            'user_id' => $user['id']
        ]);
        return;
    }

    // User is verified, create session
    $token = generate_token();
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $stmt = $db->prepare('INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'], $token, $expires_at]);

    echo json_encode([
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ],
        'token' => $token
    ]);
}

function route_verify_otp() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['email']) || !isset($data['otp'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and OTP are required']);
        return;
    }

    $email = trim($data['email']);
    $otp = trim($data['otp']);

    $db = get_db();
    $stmt = $db->prepare('SELECT id, email, password_hash, first_name, last_name, email_verification_token, email_verification_expires FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    if ($user['email_verification_token'] !== $otp) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid OTP']);
        return;
    }

    if (strtotime($user['email_verification_expires']) < time()) {
        http_response_code(401);
        echo json_encode(['error' => 'OTP has expired']);
        return;
    }

    // Mark email as verified and clear OTP
    $stmt = $db->prepare('UPDATE users SET email_verified = 1, email_verification_token = NULL, email_verification_expires = NULL WHERE id = ?');
    $stmt->execute([$user['id']]);

    // Create session
    $token = generate_token();
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $stmt = $db->prepare('INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'], $token, $expires_at]);

    echo json_encode([
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ],
        'token' => $token,
        'message' => 'Email verified successfully!'
    ]);
}

function route_resend_otp() {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        return;
    }

    $email = trim($data['email']);

    $db = get_db();
    $stmt = $db->prepare('SELECT id, email_verified FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    // Generate new OTP
    $otp = generate_otp();
    $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // For testing - skip email sending
    // if ($user['email_verified']) {
    //     // Resend login OTP
    //     if (!send_login_otp_email($email, $otp)) {
    //         http_response_code(500);
    //         echo json_encode(['error' => 'Failed to send verification email. Please try again.']);
    //         return;
    //     }
    // } else {
    //     // Resend registration OTP
    //     if (!send_otp_email($email, $otp)) {
    //         http_response_code(500);
    //         echo json_encode(['error' => 'Failed to send verification email. Please try again.']);
    //         return;
    //     }
    // }

    // Update OTP in database
    $stmt = $db->prepare('UPDATE users SET email_verification_token = ?, email_verification_expires = ? WHERE id = ?');
    $stmt->execute([$otp, $otp_expires, $user['id']]);

    echo json_encode([
        'message' => 'Verification code sent! Your new code is: ' . $otp . ' (For testing - normally sent via email)',
        'test_otp' => $otp  // Remove this in production
    ]);
}

function route_get_current_user() {
    $user = get_logged_in_user();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }
    echo json_encode(['user' => $user]);
}

function route_get_user_sessions() {
    $user = require_auth();
    $db = get_db();
    $stmt = $db->prepare('
        SELECT s.id, s.case_id, s.started_at, s.ended_at, s.score, s.feedback, c.name as case_name
        FROM simulation_sessions s
        JOIN cases c ON s.case_id = c.id
        WHERE s.user_id = ?
        ORDER BY s.started_at DESC
    ');
    $stmt->execute([$user['id']]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['sessions' => $sessions]);
}

?>