<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - Nur-Sim</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        button { padding: 10px 20px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .result { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Nur-Sim API Test</h1>

    <div class="test-section">
        <h2>Test Database Connection</h2>
        <button onclick="testDatabase()">Test Database</button>
        <div class="result" id="db-test-result"></div>
    </div>

    <div class="test-section">
        <h2>Test Registration</h2>
        <input type="email" id="test-email" placeholder="Email" value="test@example.com">
        <input type="password" id="test-password" placeholder="Password" value="password123">
        <input type="text" id="test-firstname" placeholder="First Name" value="Test">
        <input type="text" id="test-lastname" placeholder="Last Name" value="User">
        <button onclick="testRegistration()">Test Registration</button>
        <div class="result" id="registration-test-result"></div>
    </div>

    <div class="test-section">
        <h2>Test Login</h2>
        <input type="email" id="login-email" placeholder="Email" value="test@example.com">
        <input type="password" id="login-password" placeholder="Password" value="password123">
        <button onclick="testLogin()">Test Login</button>
        <div class="result" id="login-test-result"></div>
    </div>

    <div class="test-section">
        <h2>Test Cases Endpoint</h2>
        <button onclick="testCases()">Test Cases</button>
        <div class="result" id="cases-test-result"></div>
    </div>

    <script>
        const API_BASE = '/Nur-Sim/PhP/api.php';

        async function testAPIConnection() {
            const resultDiv = document.getElementById('api-test-result');
            resultDiv.innerHTML = 'Testing...';

            try {
                const response = await fetch(`${API_BASE}/`);
                const data = await response.json();
                resultDiv.innerHTML = `<pre>Status: ${response.status}\n${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                resultDiv.innerHTML = `<pre style="color: red;">Error: ${error.message}</pre>`;
            }
        }

        async function testDatabase() {
            const resultDiv = document.getElementById('db-test-result');
            resultDiv.innerHTML = 'Testing...';

            try {
                const response = await fetch(`${API_BASE}/test-db`);
                const data = await response.json();
                resultDiv.innerHTML = `<pre>Status: ${response.status}\n${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                resultDiv.innerHTML = `<pre style="color: red;">Error: ${error.message}</pre>`;
            }
        }

        async function testRegistration() {
            const resultDiv = document.getElementById('registration-test-result');
            resultDiv.innerHTML = 'Testing...';

            const email = document.getElementById('test-email').value;
            const password = document.getElementById('test-password').value;
            const firstName = document.getElementById('test-firstname').value;
            const lastName = document.getElementById('test-lastname').value;

            try {
                const response = await fetch(`${API_BASE}/auth/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        first_name: firstName,
                        last_name: lastName
                    })
                });
                const data = await response.json();
                resultDiv.innerHTML = `<pre>Status: ${response.status}\n${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                resultDiv.innerHTML = `<pre style="color: red;">Error: ${error.message}</pre>`;
            }
        }

        async function testLogin() {
            const resultDiv = document.getElementById('login-test-result');
            resultDiv.innerHTML = 'Testing...';

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            try {
                const response = await fetch(`${API_BASE}/auth/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await response.json();
                resultDiv.innerHTML = `<pre>Status: ${response.status}\n${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                resultDiv.innerHTML = `<pre style="color: red;">Error: ${error.message}</pre>`;
            }
        }

        async function testCases() {
            const resultDiv = document.getElementById('cases-test-result');
            resultDiv.innerHTML = 'Testing...';

            try {
                const response = await fetch(`${API_BASE}/cases`);
                const data = await response.json();
                resultDiv.innerHTML = `<pre>Status: ${response.status}\n${JSON.stringify(data, null, 2)}</pre>`;
            } catch (error) {
                resultDiv.innerHTML = `<pre style="color: red;">Error: ${error.message}</pre>`;
            }
        }
    </script>
</body>
</html>