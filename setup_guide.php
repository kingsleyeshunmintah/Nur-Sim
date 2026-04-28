<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Access Nur-Sim</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
        .step { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .step h3 { color: #2D8FBF; margin-top: 0; }
        .important { background: #fff3cd; border-color: #ffeaa7; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; }
        .button { display: inline-block; padding: 10px 20px; background: #2D8FBF; color: white; text-decoration: none; border-radius: 4px; margin: 10px 0; }
        .button:hover { background: #1e6b8c; }
    </style>
</head>
<body>
    <h1>🚨 Important: How to Access Nur-Sim Properly</h1>

    <div class="important">
        <strong>CRITICAL:</strong> You must access Nur-Sim through a web server (http://localhost), NOT by opening files directly in your browser. Opening HTML files directly (file:// protocol) will cause "Network error" messages.
    </div>

    <div class="step">
        <h3>Step 1: Start XAMPP</h3>
        <p>Open XAMPP Control Panel and start Apache:</p>
        <ol>
            <li>Open XAMPP Control Panel</li>
            <li>Click "Start" next to Apache</li>
            <li>Wait for Apache to turn green</li>
        </ol>
    </div>

    <div class="step">
        <h3>Step 2: Access the Site</h3>
        <p>Open your web browser and go to:</p>
        <div class="code">http://localhost/Nur-Sim/PhP/front.php</div>
        <p><strong>NOT:</strong> <code class="code">file:///C:/xampp/htdocs/Nur-Sim/PhP/front.php</code></p>
    </div>

    <div class="step">
        <h3>Step 3: Test the API</h3>
        <p>First, test if the API is working:</p>
        <div class="code">http://localhost/Nur-Sim/PhP/api_test.php</div>
        <p>This page will help you test all API endpoints.</p>
    </div>

    <div class="step">
        <h3>Step 4: Test Login/Signup (PHP Session System)</h3>
        <p>Use the new PHP-based authentication system:</p>
        <div class="code">http://localhost/Nur-Sim/PhP/login_new.php</div>
        <p>This version uses PHP sessions and form submissions - no JavaScript fetch required!</p>
        <p><strong>Features:</strong></p>
        <ul>
            <li>✅ Simple PHP session-based authentication</li>
            <li>✅ Email OTP verification (when configured)</li>
            <li>✅ No network errors or API dependencies</li>
            <li>✅ Works reliably with form submissions</li>
        </ul>
    </div>

    <div class="step">
        <h3>Step 5: Configure Email with PHPMailer</h3>
        <p>PHPMailer is already included. To enable real email sending:</p>
        <ol>
            <li>Copy <code>backend/.env.example</code> to <code>backend/.env</code></li>
            <li>Edit the .env file with your email settings:</li>
        </ol>
        <div class="code">
SMTP_HOST=smtp.gmail.com<br>
SMTP_PORT=587<br>
SMTP_USERNAME=your-email@gmail.com<br>
SMTP_PASSWORD=your-app-password<br>
SMTP_ENCRYPTION=tls
        </div>
        <p><strong>For Gmail:</strong></p>
        <ol>
            <li>Enable 2FA on your Gmail account</li>
            <li>Go to <a href="https://myaccount.google.com/apppasswords" target="_blank">Google App Passwords</a></li>
            <li>Generate an "App Password" for "Mail"</li>
            <li>Use this 16-character password (no spaces) as SMTP_PASSWORD</li>
        </ol>
        <p><strong>Test your email setup:</strong></p>
        <div class="code">
<a href="test_smtp.php">Test SMTP Connection</a><br>
<a href="debug_email.php">Test Email Sending</a>
        </div>
    </div>

    <div class="step">
        <h3>Troubleshooting</h3>
        <ul>
            <li><strong>"Network error"</strong> = You're opening files directly instead of using a web server</li>
            <li><strong>Apache won't start</strong> = Another program is using port 80 (like Skype)</li>
            <li><strong>Database errors</strong> = Check if nursim.db exists in backend/ folder</li>
            <li><strong>Emails not received</strong> = Check Gmail settings, use App Password, test with debug tools</li>
            <li><strong>SMTP connection failed</strong> = Firewall blocking, wrong credentials, or Gmail security settings</li>
            <li><strong>PHP errors</strong> = Check php_errors.log in the main directory</li>
        </ul>
        <p><strong>Debug Tools:</strong></p>
        <div class="code">
<a href="test_smtp.php">SMTP Connection Test</a><br>
<a href="debug_email.php">Email Sending Test</a><br>
<a href="test_php_login.php">PHP Login Test</a>
        </div>
    </div>

    <a href="front.php" class="button">Go to Nur-Sim Homepage</a>
    <a href="api_test.php" class="button">Test API</a>
    <a href="login_new.php" class="button">Go to Login (PHP)</a>
    <a href="login.php" class="button">Go to Login (JS)</a>
</body>
</html>