<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nur-Sim — Clinical Simulation Platform</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:        #0B1622;
      --surface:   #101F30;
      --surface2:  #142536;
      --border:    #1C3352;
      --accent:    #2D8FBF;
      --accent2:   #4FC3F7;
      --text:      #D8EEF8;
      --muted:     #5A82A0;
      --danger:    #E05252;
      --success:   #2ECC71;
      --warning:   #F39C12;
      --mono:      'DM Mono', monospace;
      --sans:      'Sora', sans-serif;
    }

    html, body { height: 100%; background: var(--bg); color: var(--text); font-family: var(--sans); }

    /* ── Layout ── */
    .app { display: flex; flex-direction: column; min-height: 100vh; }

    /* ── Topbar ── */
    .topbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 28px; border-bottom: 1px solid var(--border);
      background: var(--bg); position: sticky; top: 0; z-index: 10;
    }
    .logo { display: flex; align-items: center; gap: 10px; }
    .logo-icon {
      width: 32px; height: 32px; border-radius: 8px;
      background: var(--accent); display: grid; place-items: center;
      font-size: 16px; font-weight: 800; color: #fff;
    }
    .logo-text { font-size: 20px; font-weight: 800; letter-spacing: 3px; color: var(--text); }
    .logo-sub  { font-size: 11px; color: var(--accent2); letter-spacing: 1.5px; text-transform: uppercase; }

    /* ── Hero ── */
    .hero { padding: 48px 28px 32px; max-width: 900px; margin: 0 auto; width: 100%; }
    .hero h1 { font-size: clamp(22px, 4vw, 32px); font-weight: 800; margin-bottom: 8px; }
    .hero h1 span { color: var(--accent2); }
    .hero p  { font-size: 15px; color: var(--muted); line-height: 1.6; max-width: 520px; }

    /* ── Case Grid ── */
    .cases-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px; padding: 0 28px 60px; max-width: 900px; margin: 0 auto; width: 100%;
    }

    .case-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 14px; padding: 20px; cursor: pointer;
      transition: border-color .2s, transform .15s, box-shadow .2s;
      animation: fadeUp .35s ease both;
    }
    .case-card:hover {
      border-color: var(--accent); transform: translateY(-3px);
      box-shadow: 0 8px 28px rgba(45,143,191,.15);
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .case-card:nth-child(1) { animation-delay: .05s; }
    .case-card:nth-child(2) { animation-delay: .1s; }
    .case-card:nth-child(3) { animation-delay: .15s; }
    .case-card:nth-child(4) { animation-delay: .2s; }

    .card-head    { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
    .patient-name { font-size: 18px; font-weight: 700; }
    .diff-badge   { font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 20px; border: 1px solid; letter-spacing: .4px; white-space: nowrap; }
    .diff-Beginner     { color: #2ECC71; background: #0A3D2E; border-color: #1A6B4A; }
    .diff-Intermediate { color: #F39C12; background: #3D2E0A; border-color: #6B4A1A; }
    .diff-Advanced     { color: #E74C3C; background: #3D0A0A; border-color: #6B1A1A; }

    .diagnosis { font-size: 12px; color: var(--muted); margin-bottom: 12px; line-height: 1.5; }

    .tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
    .tag  { font-size: 10px; color: #8ABBD4; background: #112233; padding: 3px 8px; border-radius: 4px; }

    .card-footer  { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid var(--border); }
    .card-title   { font-size: 13px; color: #C0D8E8; font-weight: 500; }
    .begin-btn    { background: var(--accent); color: #fff; border: none; border-radius: 8px; padding: 7px 14px; font-size: 11px; font-weight: 700; letter-spacing: 1px; cursor: pointer; transition: background .15s; font-family: var(--mono); }
    .begin-btn:hover { background: #3AAAD8; }

    /* ── Loading / Error ── */
    .state-msg { text-align: center; padding: 60px 20px; color: var(--muted); font-size: 14px; }
    .spinner { display: inline-block; width: 32px; height: 32px; border: 3px solid var(--border); border-top-color: var(--accent2); border-radius: 50%; animation: spin .7s linear infinite; margin-bottom: 12px; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>
<body>
<div class="app">
  <nav class="topbar">
    <div class="logo">
      <div class="logo-icon">✚</div>
      <div>
        <div class="logo-text">NUR-SIM</div>
        <div class="logo-sub">Clinical Simulation Platform</div>
      </div>
    </div>
    <div style="font-size:12px;color:var(--muted);font-family:var(--mono);">v1.0.0</div>
  </nav>

  <main>
    <div class="hero">
      <h1>Select a <span>Patient Case</span></h1>
      <p>Choose a clinical scenario below to begin your simulation. You will interact with an AI-powered patient — assess, communicate, and treat safely.</p>
    </div>

    <div class="cases-grid" id="cases-grid">
      <div class="state-msg">
        <div class="spinner"></div><br>Loading cases…
      </div>
    </div>
  </main>
</div>

<script>
const API = '<?= defined("API_URL") ? API_URL : "http://localhost:8000" ?>';

async function loadCases() {
  const grid = document.getElementById('cases-grid');
  try {
    const res   = await fetch(`${API}/cases`);
    const cases = await res.json();
    grid.innerHTML = '';
    cases.forEach(c => grid.appendChild(buildCard(c)));
  } catch (e) {
    grid.innerHTML = `<div class="state-msg" style="color:#E74C3C">⚠️ Cannot connect to API.<br><small>${API}</small></div>`;
  }
}

function buildCard(c) {
  const el = document.createElement('div');
  el.className = 'case-card';
  el.innerHTML = `
    <div class="card-head">
      <span class="patient-name">${c.patient_name}, ${c.age}</span>
      <span class="diff-badge diff-${c.difficulty}">${c.difficulty}</span>
    </div>
    <div class="diagnosis">${c.diagnosis}</div>
    <div class="tags">${c.tags.map(t => `<span class="tag">${t}</span>`).join('')}</div>
    <div class="card-footer">
      <span class="card-title">${c.title}</span>
      <button class="begin-btn">BEGIN →</button>
    </div>`;
  el.querySelector('.begin-btn').addEventListener('click', () => startSession(c));
  return el;
}

async function startSession(c) {
  try {
    const res  = await fetch(`${API}/sessions/start`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ case_id: c.id, student_id: 'student-001' }),
    });
    const data = await res.json();
    // Store in sessionStorage and go to simulation page
    sessionStorage.setItem('nursim_session', JSON.stringify(data));
    sessionStorage.setItem('nursim_case',    JSON.stringify(c));
    window.location.href = 'simulation.php';
  } catch (e) {
    alert('Failed to start session. Is the API server running at ' + API + '?');
  }
}

loadCases();
</script>
</body>
</html>
