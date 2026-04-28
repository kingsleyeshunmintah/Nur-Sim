<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Nur-Sim</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <button id="homeBtn" class="btn-secondary" style="background: transparent; border: none; color: #5A82A0; cursor: pointer; font-size: 14px;">← Back to Home</button>
        </div>
        <div class="topbar-right">
            <button id="loginBtn" class="btn-secondary">Login</button>
            <button id="signupBtn" class="btn-primary">Sign Up</button>
        </div>
    </div>

    <div class="main-content" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
        <h1 style="text-align: center; margin-bottom: 40px; color: #D8EEF8;">About Nur-Sim</h1>
        
        <section style="margin-bottom: 40px;">
            <h2 style="color: #2D8FBF; margin-bottom: 20px;">Our Mission</h2>
            <p style="color: #5A82A0; line-height: 1.6; margin-bottom: 20px;">
                Nur-Sim is dedicated to revolutionizing nursing education through cutting-edge AI technology. 
                We believe that every nursing student deserves access to realistic, safe, and effective training 
                environments that prepare them for real-world clinical practice.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2D8FBF; margin-bottom: 20px;">What We Offer</h2>
            <div style="display: grid; gap: 20px;">
                <div style="background: #142536; padding: 20px; border-radius: 8px; border: 1px solid #1C3352;">
                    <h3 style="color: #D8EEF8; margin-bottom: 10px;">AI-Powered Patient Simulations</h3>
                    <p style="color: #5A82A0; margin: 0;">
                        Interact with virtual patients that respond intelligently to your clinical interventions, 
                        providing realistic feedback and adapting to different scenarios.
                    </p>
                </div>
                <div style="background: #142536; padding: 20px; border-radius: 8px; border: 1px solid #1C3352;">
                    <h3 style="color: #D8EEF8; margin-bottom: 10px;">Comprehensive Performance Analytics</h3>
                    <p style="color: #5A82A0; margin: 0;">
                        Receive detailed reports on your clinical decision-making, communication skills, 
                        and overall performance with actionable recommendations for improvement.
                    </p>
                </div>
                <div style="background: #142536; padding: 20px; border-radius: 8px; border: 1px solid #1C3352;">
                    <h3 style="color: #D8EEF8; margin-bottom: 10px;">Evidence-Based Case Library</h3>
                    <p style="color: #5A82A0; margin: 0;">
                        Practice with medically accurate scenarios covering a wide range of conditions, 
                        from routine care to critical emergencies.
                    </p>
                </div>
            </div>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2D8FBF; margin-bottom: 20px;">Technology</h2>
            <p style="color: #5A82A0; line-height: 1.6; margin-bottom: 20px;">
                Nur-Sim leverages advanced AI language models to create dynamic, context-aware patient interactions. 
                Our system integrates clinical guidelines, medical knowledge, and realistic patient personas to 
                provide an immersive learning experience.
            </p>
            <p style="color: #5A82A0; line-height: 1.6;">
                Built with modern web technologies and powered by Google's Gemini AI, Nur-Sim delivers 
                fast, responsive, and secure simulation environments accessible from any device.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2D8FBF; margin-bottom: 20px;">Our Team</h2>
            <p style="color: #5A82A0; line-height: 1.6;">
                Nur-Sim was developed by a team of experienced nurses, educators, and software engineers 
                committed to advancing healthcare education. We work closely with nursing educators and 
                clinical experts to ensure our simulations reflect real-world nursing practice.
            </p>
        </section>

        <section style="text-align: center;">
            <h2 style="color: #2D8FBF; margin-bottom: 20px;">Ready to Start Learning?</h2>
            <p style="color: #5A82A0; margin-bottom: 30px;">
                Join thousands of nursing students who are mastering clinical skills with Nur-Sim.
            </p>
            <button onclick="window.location.href='login.php'" class="btn-primary" style="padding: 12px 24px; font-size: 16px;">
                Get Started Today
            </button>
        </section>
    </div>

    <script>
        // Check if user is logged in
        const token = localStorage.getItem('authToken');
        if (token) {
            // User is logged in, update buttons
            document.getElementById('loginBtn').style.display = 'none';
            document.getElementById('signupBtn').textContent = 'Go to Dashboard';
            document.getElementById('signupBtn').addEventListener('click', () => {
                window.location.href = 'dashboard.php';
            });
        } else {
            // User not logged in
            document.getElementById('loginBtn').addEventListener('click', () => {
                window.location.href = 'login.php';
            });
            document.getElementById('signupBtn').addEventListener('click', () => {
                window.location.href = 'login.php';
            });
        }

        // Home button
        document.getElementById('homeBtn').addEventListener('click', () => {
            window.location.href = 'front.php';
        });
    </script>
</body>
</html>