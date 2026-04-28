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
            <button id="homeBtn" class="btn-secondary" style="background: transparent; border: none; color: #5A82A0; cursor: pointer; font-size: 14px;">← Back to Home</button>
        </div>
        <div class="topbar-right">
            <h1 style="color: #2D8FBF; font-size: 18px;">Nur-Sim</h1>
        </div>
    </div>
        <div class="auth-form">
            <h1>Welcome to Nur-Sim</h1>
            <p class="subtitle">Nursing Simulation Training Platform</p>
            
            <div id="login-form">
                <h2>Login</h2>
                <form id="loginForm">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" required>
                    </div>
                    <button type="submit" class="btn-primary">Login</button>
                </form>
                <p class="auth-link">Don't have an account? <a href="#" id="showSignup">Sign up</a></p>
            </div>

            <div id="signup-form" style="display: none;">
                <h2>Sign Up</h2>
                <form id="signupForm">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" id="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" id="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="signupEmail">Email:</label>
                        <input type="email" id="signupEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="signupPassword">Password:</label>
                        <input type="password" id="signupPassword" required minlength="6">
                    </div>
                    <button type="submit" class="btn-primary">Sign Up</button>
                </form>
                <p class="auth-link">Already have an account? <a href="#" id="showLogin">Login</a></p>
            </div>

            <div id="otp-form" style="display: none;">
                <h2>Email Verification</h2>
                <p>Please enter the 6-digit verification code sent to your email.</p>
                <form id="otpForm">
                    <div class="form-group">
                        <label for="otp">Verification Code:</label>
                        <input type="text" id="otp" maxlength="6" pattern="[0-9]{6}" required style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                    </div>
                    <button type="submit" class="btn-primary">Verify Email</button>
                </form>
                <p class="auth-link">
                    Didn't receive the code? <a href="#" id="resendOtp">Resend Code</a>
                </p>
                <p class="auth-link">
                    <a href="#" id="backToLogin">Back to Login</a>
                </p>
            </div>

            <div id="message" class="message" style="display: none;"></div>
        </div>
    </div>

    <script>
        // Check if accessing via file:// protocol
        if (window.location.protocol === 'file:') {
            alert('ERROR: You are opening this file directly in your browser. This will cause network errors.\n\nPlease access Nur-Sim through a web server:\nhttp://localhost/Nur-Sim/PhP/login.php\n\nSee setup_guide.php for instructions.');
            window.location.href = 'setup_guide.php';
        }

        const API_BASE = '/Nur-Sim/PhP/api.php';
        let token = localStorage.getItem('authToken');

        // Redirect if already logged in
        if (token) {
            window.location.href = 'dashboard.php';
        }

        // Home button
        document.getElementById('homeBtn').addEventListener('click', () => {
            window.location.href = 'front.php';
        });

        // Form switching
        document.getElementById('showSignup').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'block';
        });

        document.getElementById('showLogin').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });

        // Login form
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            showMessage('Connecting to server...', 'info');

            try {
                console.log('Making login request to:', `${API_BASE}/auth/login`);
                const response = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    if (data.requires_verification) {
                        // Store email for OTP verification
                        localStorage.setItem('pendingEmail', email);
                        showMessage(data.message || 'Please check your email for verification.', 'success');

                        // Show OTP form
                        document.getElementById('login-form').style.display = 'none';
                        document.getElementById('otp-form').style.display = 'block';
                    } else {
                        localStorage.setItem('authToken', data.token);
                        localStorage.setItem('user', JSON.stringify(data.user));
                        showMessage('Login successful! Redirecting...', 'success');
                        setTimeout(() => window.location.href = 'dashboard.php', 1000);
                    }
                } else {
                    showMessage(data.error || 'Login failed', 'error');
                }
            } catch (error) {
                console.error('Network error details:', error);
                showMessage('Network error: ' + error.message + '. Make sure you\'re accessing this page through a web server (http://localhost), not opening the file directly.', 'error');
            }
        });

        // Signup form
        document.getElementById('signupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;

            showMessage('Connecting to server...', 'info');

            try {
                console.log('Making signup request to:', `${API_BASE}/auth/register`);
                const response = await fetch(`${API_BASE}/auth/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ first_name: firstName, last_name: lastName, email, password })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    // Store email for OTP verification
                    localStorage.setItem('pendingEmail', email);
                    showMessage(data.message || 'Registration successful! Please check your email for verification.', 'success');

                    // Show OTP form
                    document.getElementById('signup-form').style.display = 'none';
                    document.getElementById('otp-form').style.display = 'block';
                } else {
                    showMessage(data.error || 'Registration failed', 'error');
                }
            } catch (error) {
                console.error('Network error details:', error);
                showMessage('Network error: ' + error.message + '. Make sure you\'re accessing this page through a web server (http://localhost), not opening the file directly.', 'error');
            }
        });
        });

        // OTP form
        document.getElementById('otpForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const otp = document.getElementById('otp').value;
            const email = localStorage.getItem('pendingEmail');

            if (!email) {
                showMessage('Session expired. Please try logging in again.', 'error');
                return;
            }

            showMessage('Verifying code...', 'info');

            try {
                console.log('Making OTP verification request to:', `${API_BASE}/auth/verify-otp`);
                const response = await fetch(`${API_BASE}/auth/verify-otp`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, otp })
                });

                console.log('OTP verification response status:', response.status);
                const data = await response.json();
                console.log('OTP verification response data:', data);

                if (response.ok) {
                    localStorage.setItem('authToken', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    localStorage.removeItem('pendingEmail');
                    showMessage(data.message || 'Verification successful! Redirecting...', 'success');
                    setTimeout(() => window.location.href = 'dashboard.php', 1000);
                } else {
                    showMessage(data.error || 'Verification failed', 'error');
                }
            } catch (error) {
                console.error('OTP verification network error:', error);
                showMessage('Network error: ' + error.message + '. Make sure you\'re accessing this page through a web server.', 'error');
            }
        });

        // Resend OTP
        document.getElementById('resendOtp').addEventListener('click', async (e) => {
            e.preventDefault();
            const email = localStorage.getItem('pendingEmail');

            if (!email) {
                showMessage('Session expired. Please try logging in again.', 'error');
                return;
            }

            showMessage('Sending new code...', 'info');

            try {
                console.log('Making resend OTP request to:', `${API_BASE}/auth/resend-otp`);
                const response = await fetch(`${API_BASE}/auth/resend-otp`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });

                console.log('Resend OTP response status:', response.status);
                const data = await response.json();
                console.log('Resend OTP response data:', data);

                if (response.ok) {
                    showMessage(data.message || 'Verification code sent!', 'success');
                } else {
                    showMessage(data.error || 'Failed to resend code', 'error');
                }
            } catch (error) {
                console.error('Resend OTP network error:', error);
                showMessage('Network error: ' + error.message + '. Make sure you\'re accessing this page through a web server.', 'error');
            }
        });

        // Back to login
        document.getElementById('backToLogin').addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('pendingEmail');
            document.getElementById('otp-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });

        function showMessage(text, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = text;
            messageEl.className = `message ${type}`;
            messageEl.style.display = 'block';
            setTimeout(() => messageEl.style.display = 'none', 5000);
        }
    </script>
</body>
</html>