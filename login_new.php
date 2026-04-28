<?php
session_start();
require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/backend/email.php';
require_once __DIR__ . '/backend/auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard_new.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Login processing
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $message = 'Please fill in all fields';
            $message_type = 'error';
        } else {
            $db = get_db();
            $stmt = $db->prepare('SELECT id, email, password_hash, first_name, last_name, email_verified FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['email_verified']) {
                    // Send OTP for verification
                    $otp = generate_otp();
                    $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                    $stmt = $db->prepare('UPDATE users SET email_verification_token = ?, email_verification_expires = ? WHERE id = ?');
                    $stmt->execute([$otp, $otp_expires, $user['id']]);

                    // Store user ID in session for OTP verification
                    $_SESSION['pending_user_id'] = $user['id'];
                    $_SESSION['pending_email'] = $email;

                    // Send login verification email
                    $email_result = send_login_otp_email($email, $otp);

                    if ($email_result['success']) {
                        $message = 'Please check your email for the verification code.';
                        $message_type = 'info';
                        $_SESSION['email_preview'] = $email_result['email_preview'];
                        $_SESSION['sent_otp'] = $email_result['otp_code'];
                    } else {
                        $message = 'Login succeeded, but email delivery failed. Your verification code is shown for testing below.';
                        $message_type = 'warning';
                        $_SESSION['email_error'] = $email_result['message'];
                        $_SESSION['email_error_debug'] = $email_result['raw_message'] ?? $email_result['error_info'] ?? '';
                        $_SESSION['email_preview'] = $email_result['email_preview'];
                        $_SESSION['sent_otp'] = $email_result['otp_code'];
                    }

                    $show_otp = true;
                } else {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

                    header('Location: dashboard_new.php');
                    exit;
                }
            } else {
                $message = 'Invalid email or password';
                $message_type = 'error';
            }
        }
    } elseif (isset($_POST['register'])) {
        // Registration processing
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $message = 'Please fill in all fields';
            $message_type = 'error';
        } elseif (strlen($password) < 6) {
            $message = 'Password must be at least 6 characters long';
            $message_type = 'error';
        } else {
            $db = get_db();

            // Check if email already exists
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = 'Email already registered';
                $message_type = 'error';
            } else {
                // Generate OTP
                $otp = generate_otp();
                $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $db->prepare('INSERT INTO users (email, password_hash, first_name, last_name, email_verification_token, email_verification_expires) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$email, $password_hash, $first_name, $last_name, $otp, $otp_expires]);

                $user_id = $db->lastInsertId();

                // Store for OTP verification
                $_SESSION['pending_user_id'] = $user_id;
                $_SESSION['pending_email'] = $email;

                // Send verification email
                $email_result = send_otp_email($email, $otp);

                if ($email_result['success']) {
                    $message = 'Registration successful! Please check your email for the verification code.';
                    $message_type = 'success';
                    $_SESSION['email_preview'] = $email_result['email_preview'];
                    $_SESSION['sent_otp'] = $email_result['otp_code'];
                } else {
                    $message = 'Registration successful, but email sending failed. Your verification code is: ' . $otp . ' (shown for testing)';
                    $message_type = 'warning';
                    $_SESSION['email_error'] = $email_result['message'];
                    $_SESSION['email_preview'] = $email_result['email_preview'];
                    $_SESSION['sent_otp'] = $email_result['otp_code'];
                }

                $show_otp = true;
            }
        }
    } elseif (isset($_POST['verify_otp'])) {
        // OTP verification
        $otp = trim($_POST['otp']);
        $user_id = $_SESSION['pending_user_id'] ?? null;

        if (!$user_id || empty($otp)) {
            $message = 'Invalid verification code';
            $message_type = 'error';
        } else {
            $db = get_db();
            $stmt = $db->prepare('SELECT email_verification_token, email_verification_expires FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['email_verification_token'] === $otp && strtotime($user['email_verification_expires']) > time()) {
                // Mark email as verified
                $stmt = $db->prepare('UPDATE users SET email_verified = 1, email_verification_token = NULL, email_verification_expires = NULL WHERE id = ?');
                $stmt->execute([$user_id]);

                // Log the user in
                $stmt = $db->prepare('SELECT email, first_name, last_name FROM users WHERE id = ?');
                $stmt->execute([$user_id]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];

                // Clear pending verification session variables
                unset($_SESSION['pending_user_id']);
                unset($_SESSION['pending_email']);
                unset($_SESSION['email_preview']);
                unset($_SESSION['sent_otp']);
                unset($_SESSION['email_error']);

                header('Location: dashboard_new.php');
                exit;
            } else {
                // Provide detailed error message with hints
                $error_hints = [];
                if (!$user) {
                    $error_hints[] = "User account not found";
                } elseif ($user['email_verification_token'] !== $otp) {
                    $error_hints[] = "Code doesn't match - check for typos";
                    if (isset($_SESSION['sent_otp'])) {
                        $error_hints[] = "Expected code: " . $_SESSION['sent_otp'] . " (for testing)";
                    }
                } elseif (strtotime($user['email_verification_expires']) <= time()) {
                    $error_hints[] = "Code has expired (valid for 10 minutes)";
                }

                $message = 'Invalid or expired verification code. ' . implode('. ', $error_hints);
                $message_type = 'error';
                $show_otp = true;
            }
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Resend OTP
        $user_id = $_SESSION['pending_user_id'] ?? null;
        $email = $_SESSION['pending_email'] ?? null;

        if (!$user_id || !$email) {
            $message = 'Session expired. Please try logging in again.';
            $message_type = 'error';
        } else {
            $db = get_db();
            $stmt = $db->prepare('SELECT email_verified FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $message = 'User account not found.';
                $message_type = 'error';
            } elseif ($user['email_verified']) {
                $message = 'Email is already verified.';
                $message_type = 'info';
            } else {
                // Generate new OTP
                $otp = generate_otp();
                $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                $stmt = $db->prepare('UPDATE users SET email_verification_token = ?, email_verification_expires = ? WHERE id = ?');
                $stmt->execute([$otp, $otp_expires, $user_id]);

                // Send new verification email
                $email_result = send_otp_email($email, $otp);

                if ($email_result['success']) {
                    $message = 'New verification code sent! Please check your email.';
                    $message_type = 'success';
                    $_SESSION['email_preview'] = $email_result['email_preview'];
                    $_SESSION['sent_otp'] = $email_result['otp_code'];
                } else {
                    $message = 'Email delivery failed. Your new verification code is shown for testing below.';
                    $message_type = 'warning';
                    $_SESSION['email_error'] = $email_result['message'];
                    $_SESSION['email_error_debug'] = $email_result['raw_message'] ?? $email_result['error_info'] ?? '';
                    $_SESSION['email_preview'] = $email_result['email_preview'];
                    $_SESSION['sent_otp'] = $email_result['otp_code'];
                }
            }
        }
        $show_otp = true;
    }
}

// Determine which form to show
$show_login = !isset($_GET['action']) || $_GET['action'] !== 'register';
$show_register = isset($_GET['action']) && $_GET['action'] === 'register';
$show_otp = isset($show_otp) || isset($_SESSION['pending_user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nur-Sim</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <button onclick="window.location.href='front.php'" class="btn-secondary" style="background: transparent; border: none; color: #5A82A0; cursor: pointer; font-size: 14px;">← Back to Home</button>
        </div>
        <div class="topbar-right">
            <h1 style="color: #2D8FBF; font-size: 18px;">Nur-Sim</h1>
        </div>
    </div>

    <div class="auth-form">
        <h1>Welcome to Nur-Sim</h1>
        <p class="subtitle">Nursing Simulation Training Platform</p>

        <?php if ($message): ?>
            <div class="message message-<?php echo $message_type; ?>" style="display: block; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($show_otp): ?>
            <!-- OTP Verification Form -->
            <div id="otp-form">
                <h2>Email Verification</h2>
                <p>Please enter the 6-digit verification code sent to your email.</p>

                <?php if (isset($_SESSION['email_error'])): ?>
                    <div class="message message-warning" style="display: block; margin-bottom: 15px;">
                        <strong>Email Notice:</strong> <?php echo htmlspecialchars($_SESSION['email_error']); ?>
                        <p style="margin-top: 8px; font-size: 13px; color: #ddd;">
                            Hint: If this continues, check your SMTP credentials, Gmail App Password, and local OpenSSL/CA certificate support.
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['email_error_debug']) && $_SESSION['email_error_debug']): ?>
                    <details style="margin-bottom: 15px;">
                        <summary style="cursor: pointer; color: #2D8FBF; font-weight: bold;">View SMTP debug details</summary>
                        <pre style="white-space: pre-wrap; color: #fff; background: #111; padding: 12px; border-radius: 8px;"><?php echo htmlspecialchars($_SESSION['email_error_debug']); ?></pre>
                    </details>
                <?php endif; ?>

                <?php if (isset($_SESSION['sent_otp'])): ?>
                    <div class="message message-info" style="display: block; margin-bottom: 15px;">
                        <strong>For Testing:</strong> Verification code is: <code style="font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars($_SESSION['sent_otp']); ?></code>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="otp">Verification Code:</label>
                        <input type="text" id="otp" name="otp" maxlength="6" pattern="[0-9]{6}" required style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                    </div>
                    <button type="submit" name="verify_otp" class="btn-primary">Verify Email</button>
                    <button type="submit" name="resend_otp" class="btn-secondary" style="margin-left: 10px;">Resend Code</button>
                </form>

                <?php if (isset($_SESSION['email_preview'])): ?>
                    <details style="margin-top: 20px;">
                        <summary style="cursor: pointer; color: #2D8FBF; font-weight: bold;">📧 View Email Preview</summary>
                        <div style="margin-top: 10px; padding: 15px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; max-height: 300px; overflow-y: auto;">
                            <iframe srcdoc="<?php echo htmlspecialchars($_SESSION['email_preview']); ?>" style="width: 100%; height: 250px; border: none;"></iframe>
                        </div>
                    </details>
                <?php endif; ?>

                <p class="auth-link">
                    <a href="?action=login">Back to Login</a> | <a href="?action=register">Back to Register</a>
                </p>
            </div>
        <?php elseif ($show_register): ?>
            <!-- Registration Form -->
            <div id="register-form">
                <h2>Sign Up</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>
                    <button type="submit" name="register" class="btn-primary">Sign Up</button>
                </form>
                <p class="auth-link">Already have an account? <a href="?action=login">Login</a></p>
            </div>
        <?php else: ?>
            <!-- Login Form -->
            <div id="login-form">
                <h2>Login</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn-primary">Login</button>
                </form>
                <p class="auth-link">Don't have an account? <a href="?action=register">Sign up</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>