<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nur-Sim - Nursing Simulation Training</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .hero {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #0B1622 0%, #101F30 100%);
            color: white;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #2D8FBF, #4FC3F7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #D8EEF8;
        }
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .features {
            padding: 60px 20px;
            background: #101F30;
        }
        .features h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #D8EEF8;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .feature-card {
            background: #142536;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #1C3352;
            text-align: center;
        }
        .feature-card svg {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
            color: #2D8FBF;
        }
        .feature-card h3 {
            color: #D8EEF8;
            margin-bottom: 15px;
        }
        .feature-card p {
            color: #5A82A0;
            line-height: 1.6;
        }
        .sample-cases {
            padding: 60px 20px;
            background: #0B1622;
        }
        .sample-cases h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #D8EEF8;
        }
        .cases-carousel {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .case-card {
            flex: 0 0 300px;
            background: #142536;
            border-radius: 12px;
            border: 1px solid #1C3352;
            overflow: hidden;
            scroll-snap-align: start;
        }
        .case-image {
            height: 200px;
            background: #1C3352;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #2D8FBF;
        }
        .case-content {
            padding: 20px;
        }
        .case-content h3 {
            color: #D8EEF8;
            margin-bottom: 10px;
        }

        .case-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .case-header h3 {
            margin-bottom: 0;
            flex-grow: 1;
        }

        .difficulty-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .difficulty-badge.beginner {
            background: #4CAF50;
            color: white;
        }

        .difficulty-badge.intermediate {
            background: #FF9800;
            color: white;
        }

        .difficulty-badge.advanced {
            background: #F44336;
            color: white;
        }

        .vital-signs-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0;
        }

        .vital {
            background: #1C3352;
            color: #2D8FBF;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .case-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }

        .tag {
            background: #2D8FBF;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        .footer {
            padding: 40px 20px;
            background: #101F30;
            text-align: center;
            color: #5A82A0;
        }
        .footer a {
            color: #2D8FBF;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

        /* Learning Modules */
        .learning-modules {
            padding: 60px 20px;
            background: #0B1622;
        }

        .learning-modules h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #D8EEF8;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .module-card {
            background: #142536;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #1C3352;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .module-card:hover {
            transform: translateY(-5px);
        }

        .module-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .module-card h3 {
            color: #D8EEF8;
            margin-bottom: 15px;
        }

        .module-card p {
            color: #5A82A0;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .module-stats {
            display: flex;
            justify-content: space-between;
            color: #2D8FBF;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Scroll Reveal Animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-left {
            transform: translateX(-30px);
        }

        .reveal-right {
            transform: translateX(30px);
        }

        /* Auto-scroll carousel */
        .cases-carousel {
            scroll-behavior: smooth;
        }

        .case-card {
            transition: transform 0.3s ease;
        }

        .case-card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <h1>Nur-Sim</h1>
        </div>
        <div class="topbar-right">
            <button id="loginBtn" class="btn-secondary">Login</button>
            <button id="signupBtn" class="btn-primary">Sign Up</button>
        </div>
    </div>

    <section class="hero reveal">
        <h1>Master Nursing Skills Through AI-Powered Simulation</h1>
        <p>Experience realistic patient interactions, practice clinical decision-making, and receive detailed performance feedback in a safe, virtual environment.</p>
        <div class="hero-buttons">
            <button id="getStartedBtn" class="btn-primary">Get Started</button>
            <button id="learnMoreBtn" class="btn-secondary">Learn More</button>
        </div>
    </section>

    <section class="features reveal">
        <h2>Advanced Nursing Simulation Features</h2>
        <div class="features-grid">
            <div class="feature-card reveal reveal-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                </svg>
                <h3>AI-Powered Virtual Patients</h3>
                <p>Interact with intelligent virtual patients that respond realistically to your assessments, questions, and interventions with dynamic vital signs and clinical presentations.</p>
            </div>
            <div class="feature-card reveal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
                <h3>Real-Time Vital Signs Monitoring</h3>
                <p>Monitor dynamic vital signs that change based on your interventions and patient responses. Learn to recognize critical changes and respond appropriately.</p>
            </div>
            <div class="feature-card reveal reveal-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 1 18 18a8.967 8.967 0 0 1-6 2.292m0-14.25v14.25" />
                </svg>
                <h3>Comprehensive Case Library</h3>
                <p>Access hundreds of evidence-based clinical scenarios ranging from routine check-ups to life-threatening emergencies across all medical specialties.</p>
            </div>
            <div class="feature-card reveal reveal-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m-3.75 3h3.75M6 21h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z" />
                </svg>
                <h3>Interactive Assessment Tools</h3>
                <p>Use digital assessment tools including stethoscopes, blood pressure cuffs, and examination techniques with realistic audio and visual feedback.</p>
            </div>
            <div class="feature-card reveal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <h3>Instant Performance Feedback</h3>
                <p>Receive immediate, detailed feedback on your clinical decisions, communication skills, and patient care with actionable improvement suggestions.</p>
            </div>
            <div class="feature-card reveal reveal-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 0 1 5.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                <h3>Progress Analytics & Tracking</h3>
                <p>Track your learning progress with detailed analytics, skill assessments, and personalized learning paths to guide your nursing education journey.</p>
            </div>
        </div>
    </section>

    <section class="learning-modules reveal">
        <h2>Structured Learning Paths</h2>
        <div class="modules-grid">
            <div class="module-card">
                <div class="module-icon">🏥</div>
                <h3>Medical-Surgical Nursing</h3>
                <p>Master assessment, intervention, and care planning for adult patients with complex medical conditions.</p>
                <div class="module-stats">
                    <span>25 Cases</span>
                    <span>Beginner to Advanced</span>
                </div>
            </div>
            <div class="module-card">
                <div class="module-icon">🍼</div>
                <h3>Maternal & Newborn Care</h3>
                <p>Develop expertise in prenatal care, labor & delivery, and newborn assessment and care.</p>
                <div class="module-stats">
                    <span>18 Cases</span>
                    <span>Intermediate Level</span>
                </div>
            </div>
            <div class="module-card">
                <div class="module-icon">👶</div>
                <h3>Pediatric Nursing</h3>
                <p>Learn age-specific assessment and care for infants, children, and adolescents.</p>
                <div class="module-stats">
                    <span>20 Cases</span>
                    <span>All Levels</span>
                </div>
            </div>
            <div class="module-card">
                <div class="module-icon">🧠</div>
                <h3>Mental Health Nursing</h3>
                <p>Build therapeutic communication and crisis intervention skills for psychiatric care.</p>
                <div class="module-stats">
                    <span>15 Cases</span>
                    <span>Advanced Level</span>
                </div>
            </div>
        </div>
    </section>

    <section class="sample-cases reveal">
        <h2>Sample Patient Cases</h2>
        <div class="cases-carousel" id="cases-carousel">
            <!-- Cases will be loaded dynamically -->
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 Nur-Sim. <a href="about.php">About</a> | <a href="contact.php">Contact</a></p>
    </footer>

    <script>
        // Check if accessing via file:// protocol
        if (window.location.protocol === 'file:') {
            alert('ERROR: You are opening this file directly in your browser. This will cause network errors.\n\nPlease access Nur-Sim through a web server:\nhttp://localhost/Nur-Sim/PhP/front.php\n\nSee setup_guide.php for instructions.');
            window.location.href = 'setup_guide.php';
        }

        const API_BASE = '/Nur-Sim/PhP/api.php';

        // Check if user is logged in
        const token = localStorage.getItem('authToken');
        if (token) {
            // User is logged in, redirect to dashboard
            window.location.href = 'dashboard.php';
        }

        // Button event listeners
        document.getElementById('loginBtn').addEventListener('click', () => {
            window.location.href = 'login_new.php';
        });

        document.getElementById('signupBtn').addEventListener('click', () => {
            window.location.href = 'login_new.php?action=register';
        });

        document.getElementById('getStartedBtn').addEventListener('click', () => {
            window.location.href = 'login_new.php?action=register';
        });

        document.getElementById('learnMoreBtn').addEventListener('click', () => {
            document.querySelector('.features').scrollIntoView({ behavior: 'smooth' });
        });

        // Load sample cases
        async function loadSampleCases() {
            try {
                const response = await fetch(`${API_BASE}/cases`);
                const cases = await response.json();
                
                const carousel = document.getElementById('cases-carousel');
                cases.slice(0, 6).forEach(caseData => {  // Show first 6 cases
                    const caseCard = document.createElement('div');
                    caseCard.className = 'case-card';
                    caseCard.innerHTML = `
                        <div class="case-image">
                            ${caseData.profile_image ? 
                                `<img src="${API_BASE}/${caseData.profile_image}" alt="${caseData.patient_name}" style="width: 100%; height: 100%; object-fit: cover;">` :
                                caseData.patient_name.charAt(0)
                            }
                        </div>
                        <div class="case-content">
                            <div class="case-header">
                                <h3>${caseData.title}</h3>
                                <div class="difficulty-badge ${caseData.difficulty.toLowerCase()}">${caseData.difficulty}</div>
                            </div>
                            <p><strong>Patient:</strong> ${caseData.patient_name}, ${caseData.age}y/o ${caseData.gender}</p>
                            <p><strong>Diagnosis:</strong> ${caseData.diagnosis}</p>
                            <div class="vital-signs-preview">
                                ${caseData.bp ? `<span class="vital">BP: ${caseData.bp}</span>` : ''}
                                ${caseData.hr ? `<span class="vital">HR: ${caseData.hr}</span>` : ''}
                                ${caseData.rr ? `<span class="vital">RR: ${caseData.rr}</span>` : ''}
                                ${caseData.temp ? `<span class="vital">Temp: ${caseData.temp}</span>` : ''}
                                ${caseData.spo2 ? `<span class="vital">SpO2: ${caseData.spo2}</span>` : ''}
                            </div>
                            <div class="case-tags">
                                ${caseData.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
                            </div>
                        </div>
                    `;
                    carousel.appendChild(caseCard);
                });
            } catch (error) {
                console.error('Failed to load sample cases:', error);
            }
        }

        // Initialize
        loadSampleCases();
        initScrollReveals();
        initAutoScroll();

        // Scroll reveal functionality
        function initScrollReveals() {
            const reveals = document.querySelectorAll('.reveal');
            
            function revealOnScroll() {
                reveals.forEach(reveal => {
                    const elementTop = reveal.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        reveal.classList.add('active');
                    }
                });
            }
            
            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll(); // Check on load
        }

        // Auto-scroll functionality for cases
        function initAutoScroll() {
            const carousel = document.getElementById('cases-carousel');
            let scrollInterval;
            let isHovered = false;

            function startAutoScroll() {
                if (!isHovered) {
                    scrollInterval = setInterval(() => {
                        if (carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth) {
                            carousel.scrollLeft = 0; // Reset to beginning
                        } else {
                            carousel.scrollLeft += 2; // Smooth scroll
                        }
                    }, 50);
                }
            }

            function stopAutoScroll() {
                clearInterval(scrollInterval);
            }

            // Start auto-scroll
            startAutoScroll();

            // Pause on hover
            carousel.addEventListener('mouseenter', () => {
                isHovered = true;
                stopAutoScroll();
            });

            carousel.addEventListener('mouseleave', () => {
                isHovered = false;
                startAutoScroll();
            });

            // Allow manual scrolling
            carousel.addEventListener('wheel', (e) => {
                e.preventDefault();
                carousel.scrollLeft += e.deltaY;
            });
        }
    </script>
</body>
</html>