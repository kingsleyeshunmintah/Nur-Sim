<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nur-Sim — Case Selection</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #0B1622; --surface: #101F30; --surface2: #142536;
      --border: #1C3352; --accent: #2D8FBF; --accent2: #4FC3F7;
      --text: #D8EEF8; --muted: #5A82A0; --danger: #E05252;
      --sans: 'Sora', sans-serif; --mono: 'DM Mono', monospace;
    }
    html, body { height: 100%; background: var(--bg); color: var(--text); font-family: var(--sans); }

    .container { display: flex; height: 100vh; }

    .sidebar { width: 250px; background: var(--surface); border-right: 1px solid var(--border); padding: 20px 0; flex-shrink: 0; }
    .sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid var(--border); margin-bottom: 20px; }
    .sidebar-header h2 { font-size: 24px; font-weight: 800; color: var(--accent); }
    .sidebar-nav { padding: 0 20px; }
    .nav-link { display: block; color: var(--muted); text-decoration: none; padding: 10px 0; border-radius: 6px; transition: background .2s, color .2s; }
    .nav-link:hover, .nav-link.active { background: var(--surface2); color: var(--accent2); }

    .main-content { flex: 1; padding: 20px; overflow-y: auto; }

    .header { text-align: center; margin-bottom: 40px; }
    .header h1 { font-size: 32px; font-weight: 800; margin-bottom: 8px; }
    .header p { color: var(--muted); font-size: 16px; }

    .cases-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }

    .case-card {
      background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
      padding: 20px; cursor: pointer; transition: border-color .2s, transform .2s;
    }
    .case-card:hover { border-color: var(--accent); transform: translateY(-2px); }

    .case-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .case-avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
    .case-info { flex: 1; }
    .case-title { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
    .case-patient { font-size: 14px; color: var(--accent2); }

    .case-meta { display: flex; gap: 12px; margin-bottom: 12px; }
    .case-meta span { font-size: 12px; color: var(--muted); background: var(--surface2); padding: 4px 8px; border-radius: 6px; }
    .case-desc { font-size: 14px; color: var(--text); line-height: 1.4; }

    .loading { text-align: center; padding: 40px; color: var(--muted); }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="sidebar-header">
        <h2>Nur-Sim</h2>
      </div>
      <nav class="sidebar-nav">
        <a href="index.php" class="nav-link active">Case Selection</a>
        <a href="admin.php" class="nav-link">Admin Panel</a>
        <a href="#" class="nav-link">Reports</a>
        <a href="#" class="nav-link">Settings</a>
      </nav>
    </div>

    <div class="main-content">
      <div class="header">
        <h1>Case Selection</h1>
        <p>Choose a patient case to start your simulation</p>
      </div>

      <div id="cases-container">
        <div class="loading">Loading cases...</div>
      </div>
    </div>
  </div>

  <script>
    const API_URL = 'http://localhost/Nur-Sim/PhP/api';

    async function loadCases() {
      try {
        const res = await fetch(`${API_URL}/cases`);
        const cases = await res.json();

        const container = document.getElementById('cases-container');
        container.innerHTML = `
          <div class="cases-grid">
            ${cases.map(c => `
              <div class="case-card" onclick="startCase('${c.id}')">
                <div class="case-header">
                  <img src="${c.profile_image ? '../backend/' + c.profile_image : 'https://via.placeholder.com/60x60?text=' + c.patient_name.charAt(0)}" alt="${c.patient_name}" class="case-avatar">
                  <div class="case-info">
                    <div class="case-title">${c.title}</div>
                    <div class="case-patient">${c.patient_name}, ${c.age} years old</div>
                  </div>
                </div>
                <div class="case-meta">
                  <span>${c.difficulty}</span>
                  <span>${c.diagnosis}</span>
                </div>
                <div class="case-desc">${c.tags.join(', ')}</div>
              </div>
            `).join('')}
          </div>
        `;
      } catch (err) {
        document.getElementById('cases-container').innerHTML = '<div class="loading">Error loading cases. Check console.</div>';
        console.error(err);
      }
    }

    function startCase(caseId) {
      // Start session via API
      fetch(`${API_URL}/sessions/start`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ case_id: caseId, student_id: 'student-001' })
      })
      .then(res => res.json())
      .then(data => {
        if (data.session_id) {
          // Store session and case in sessionStorage
          sessionStorage.setItem('nursim_session', JSON.stringify(data));
          sessionStorage.setItem('nursim_case', JSON.stringify(data.case));
          // Redirect to simulation
          window.location.href = `simulation.php`;
        } else {
          alert('Failed to start session: ' + JSON.stringify(data));
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error starting session');
      });
    }

    loadCases();
  </script>
</body>
</html>