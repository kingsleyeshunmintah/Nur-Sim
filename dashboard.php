<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nur-Sim</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <button id="homeBtn" class="btn-secondary" style="background: transparent; border: none; color: #5A82A0; cursor: pointer; font-size: 14px;">← Back to Home</button>
            <h1>Nur-Sim</h1>
        </div>
        <div class="topbar-right">
            <span id="userName"></span>
            <button id="logoutBtn" class="btn-secondary">Logout</button>
        </div>
    </div>

    <div class="sidebar">
        <a href="#cases" class="sidebar-link active" onclick="showSection('cases')">Cases</a>
        <a href="#reports" class="sidebar-link" onclick="showSection('reports')">Reports</a>
        <a href="#settings" class="sidebar-link" onclick="showSection('settings')">Settings</a>
        <a href="#about" class="sidebar-link" onclick="showSection('about')">About</a>
    </div>

    <div class="main-content">
        <div id="cases-section" class="section active">
            <h2>Available Cases</h2>
            <div id="cases-grid" class="cases-grid"></div>
        </div>

        <div id="reports-section" class="section">
            <h2>Your Simulation Reports</h2>
            <div id="reports-list"></div>
        </div>

        <div id="settings-section" class="section">
            <h2>Account Settings</h2>
            <div class="settings-form">
                <h3>Profile Information</h3>
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" readonly>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" readonly>
                </div>
                <p class="settings-note">Profile editing will be available in a future update.</p>
            </div>
        </div>

        <div id="about-section" class="section">
            <h2>About Nur-Sim</h2>
            <div class="about-content">
                <p>Nur-Sim is an advanced nursing simulation training platform designed to help nursing students develop clinical decision-making skills through interactive patient scenarios.</p>
                
                <h3>Features</h3>
                <ul>
                    <li>Realistic patient interactions powered by AI</li>
                    <li>Comprehensive case library with various medical conditions</li>
                    <li>Detailed performance reports and feedback</li>
                    <li>Progress tracking and analytics</li>
                    <li>Customizable simulation parameters</li>
                </ul>

                <h3>Contact</h3>
                <p>For support or inquiries, please contact our development team.</p>
            </div>
        </div>
    </div>

    <div id="message" class="message" style="display: none;"></div>

    <script>
        const API_BASE = '/Nur-Sim/PhP/api.php';
        let currentUser = null;
        let token = localStorage.getItem('authToken');

        // Check authentication
        if (!token) {
            window.location.href = 'login.php';
        }

        // Load user data
        async function loadUser() {
            try {
                const response = await fetch(`${API_BASE}/auth/me`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if (response.ok) {
                    currentUser = await response.json();
                    document.getElementById('userName').textContent = `${currentUser.user.first_name} ${currentUser.user.last_name}`;
                    document.getElementById('firstName').value = currentUser.user.first_name;
                    document.getElementById('lastName').value = currentUser.user.last_name;
                    document.getElementById('email').value = currentUser.user.email;
                } else {
                    logout();
                }
            } catch (error) {
                logout();
            }
        }

        // Load cases
        async function loadCases() {
            try {
                const response = await fetch(`${API_BASE}/cases`);
                const cases = await response.json();
                const casesGrid = document.getElementById('cases-grid');
                casesGrid.innerHTML = '';

                cases.forEach(caseData => {
                    const caseCard = document.createElement('div');
                    caseCard.className = 'case-card';
                    caseCard.innerHTML = `
                        <div class="case-image">
                            <img src="${caseData.profile_image ? '../backend/' + caseData.profile_image : 'https://via.placeholder.com/100x100?text=' + caseData.patient_name.charAt(0)}" 
                                 alt="${caseData.patient_name}" 
                                 onerror="this.src='https://via.placeholder.com/100x100?text=' + '${caseData.patient_name.charAt(0)}'">
                        </div>
                        <div class="case-info">
                            <h3>${caseData.title}</h3>
                            <p><strong>Patient:</strong> ${caseData.patient_name}, ${caseData.age}y/o, ${caseData.gender}</p>
                            <p><strong>Diagnosis:</strong> ${caseData.diagnosis}</p>
                            <p><strong>Difficulty:</strong> ${caseData.difficulty}</p>
                            <div class="case-tags">
                                ${caseData.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
                            </div>
                            ${caseData.bp || caseData.hr || caseData.rr || caseData.temp || caseData.spo2 ? 
                                `<div class="case-vitals">
                                    <h4>Current Vitals:</h4>
                                    ${caseData.bp ? `<span>BP: ${caseData.bp}</span>` : ''}
                                    ${caseData.hr ? `<span>HR: ${caseData.hr}</span>` : ''}
                                    ${caseData.rr ? `<span>RR: ${caseData.rr}</span>` : ''}
                                    ${caseData.temp ? `<span>Temp: ${caseData.temp}</span>` : ''}
                                    ${caseData.spo2 ? `<span>SpO2: ${caseData.spo2}</span>` : ''}
                                </div>` : ''}
                        </div>
                        <button class="btn-primary start-case" data-case-id="${caseData.id}">Start Simulation</button>
                    `;
                    casesGrid.appendChild(caseCard);
                });

                // Add event listeners for start buttons
                document.querySelectorAll('.start-case').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const caseId = e.target.dataset.caseId;
                        startSimulation(caseId);
                    });
                });
            } catch (error) {
                showMessage('Failed to load cases', 'error');
            }
        }

        // Load user sessions/reports
        async function loadReports() {
            try {
                const response = await fetch(`${API_BASE}/user/sessions`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await response.json();
                const reportsList = document.getElementById('reports-list');
                reportsList.innerHTML = '';

                if (data.sessions.length === 0) {
                    reportsList.innerHTML = '<p>No simulation sessions found. Start your first case to see reports here.</p>';
                    return;
                }

                data.sessions.forEach(session => {
                    const reportItem = document.createElement('div');
                    reportItem.className = 'report-item';
                    reportItem.innerHTML = `
                        <h3>${session.case_name}</h3>
                        <p><strong>Started:</strong> ${new Date(session.started_at).toLocaleString()}</p>
                        <p><strong>Status:</strong> ${session.ended_at ? 'Completed' : 'In Progress'}</p>
                        ${session.score ? `<p><strong>Score:</strong> ${session.score}/100</p>` : ''}
                        ${session.ended_at ? `<p><strong>Ended:</strong> ${new Date(session.ended_at).toLocaleString()}</p>` : ''}
                        ${session.feedback ? `<div class="report-feedback"><strong>Feedback:</strong><br>${session.feedback}</div>` : ''}
                    `;
                    reportsList.appendChild(reportItem);
                });
            } catch (error) {
                showMessage('Failed to load reports', 'error');
            }
        }

        // Start simulation
        async function startSimulation(caseId) {
            try {
                const response = await fetch(`${API_BASE}/sessions/start`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ case_id: caseId })
                });

                const data = await response.json();
                if (response.ok) {
                    // Store session data and redirect to simulation
                    sessionStorage.setItem('currentSession', JSON.stringify(data));
                    window.location.href = 'simulation.php';
                } else {
                    showMessage(data.error || 'Failed to start session', 'error');
                }
            } catch (error) {
                showMessage('Failed to start simulation', 'error');
            }
        }

        // Section switching
        function showSection(sectionName) {
            // Update sidebar active state
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`[onclick="showSection('${sectionName}')"]`).classList.add('active');

            // Show selected section
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(`${sectionName}-section`).classList.add('active');

            // Load section data
            if (sectionName === 'reports') {
                loadReports();
            }
        }

        // Logout
        function logout() {
            localStorage.removeItem('authToken');
            localStorage.removeItem('user');
            window.location.href = 'login.php';
        }

        // Event listeners
        document.getElementById('logoutBtn').addEventListener('click', logout);
        document.getElementById('homeBtn').addEventListener('click', () => {
            window.location.href = 'front.php';
        });

        // Utility functions
        function showMessage(text, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = text;
            messageEl.className = `message ${type}`;
            messageEl.style.display = 'block';
            setTimeout(() => messageEl.style.display = 'none', 5000);
        }

        // Initialize
        loadUser();
        loadCases();
    </script>
</body>
</html>