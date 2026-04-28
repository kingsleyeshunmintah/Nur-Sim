<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_new.php');
    exit;
}

require_once __DIR__ . '/backend/config.php';
require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/backend/cases.php';

// Get user information
$user_name = $_SESSION['user_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? '';

// Get available cases
$cases = get_cases();
?>
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
            <button onclick="window.location.href='front.php'" class="btn-secondary" style="background: transparent; border: none; color: #5A82A0; cursor: pointer; font-size: 14px;">← Back to Home</button>
            <h1>Nur-Sim</h1>
        </div>
        <div class="topbar-right">
            <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
            <button onclick="window.location.href='logout.php'" class="btn-secondary">Logout</button>
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
            <div id="cases-grid" class="cases-grid">
                <?php foreach ($cases as $case): ?>
                    <div class="case-card" onclick="startCase(<?php echo $case['id']; ?>)">
                        <div class="case-header">
                            <h3><?php echo htmlspecialchars($case['title']); ?></h3>
                            <span class="difficulty <?php echo strtolower($case['difficulty']); ?>">
                                <?php echo htmlspecialchars($case['difficulty']); ?>
                            </span>
                        </div>
                        <div class="case-description">
                            <?php echo htmlspecialchars($case['description']); ?>
                        </div>
                        <div class="case-meta">
                            <span>Duration: <?php echo htmlspecialchars($case['estimated_duration']); ?> min</span>
                            <span>Category: <?php echo htmlspecialchars($case['category']); ?></span>
                        </div>
                        <div class="case-vitals">
                            <div class="vital-item">
                                <span class="vital-label">BP:</span>
                                <span class="vital-value"><?php echo htmlspecialchars($case['vital_bp'] ?? '120/80'); ?></span>
                            </div>
                            <div class="vital-item">
                                <span class="vital-label">HR:</span>
                                <span class="vital-value"><?php echo htmlspecialchars($case['vital_hr'] ?? '72'); ?> bpm</span>
                            </div>
                            <div class="vital-item">
                                <span class="vital-label">Temp:</span>
                                <span class="vital-value"><?php echo htmlspecialchars($case['vital_temp'] ?? '98.6'); ?>°F</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="reports-section" class="section">
            <h2>Your Simulation Reports</h2>
            <div id="reports-list">
                <p>Reports functionality coming soon...</p>
            </div>
        </div>

        <div id="settings-section" class="section">
            <h2>Account Settings</h2>
            <div class="settings-form">
                <h3>Profile Information</h3>
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" value="<?php echo htmlspecialchars(explode(' ', $user_name)[0] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" value="<?php echo htmlspecialchars(explode(' ', $user_name)[1] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                </div>
            </div>
        </div>

        <div id="about-section" class="section">
            <h2>About Nur-Sim</h2>
            <p>Nur-Sim is an advanced nursing simulation training platform designed to provide realistic patient care scenarios for nursing education.</p>
            <p>Features include:</p>
            <ul>
                <li>Interactive patient cases with vital signs monitoring</li>
                <li>Real-time assessment and decision-making</li>
                <li>Comprehensive reporting and analytics</li>
                <li>Email verification and secure authentication</li>
            </ul>
        </div>
    </div>

    <script>
        function showSection(sectionName) {
            // Hide all sections
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => section.classList.remove('active'));

            // Remove active class from all sidebar links
            const links = document.querySelectorAll('.sidebar-link');
            links.forEach(link => link.classList.remove('active'));

            // Show selected section
            document.getElementById(sectionName + '-section').classList.add('active');

            // Add active class to clicked link
            event.target.classList.add('active');
        }

        function startCase(caseId) {
            window.location.href = 'frontend/simulation.php?case_id=' + caseId;
        }
    </script>
</body>
</html>