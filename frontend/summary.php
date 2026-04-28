<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nur-Sim — Performance Report</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #0B1622; --surface: #101F30; --surface2: #142536;
      --border: #1C3352; --accent: #2D8FBF; --accent2: #4FC3F7;
      --text: #D8EEF8; --muted: #5A82A0;
      --sans: 'Sora', sans-serif; --mono: 'DM Mono', monospace;
    }
    html, body { background: var(--bg); color: var(--text); font-family: var(--sans); min-height: 100%; }

    .topbar {
      display: flex; align-items: center; justify-content: space-between;
      padding: 14px 28px; border-bottom: 1px solid var(--border);
      position: sticky; top: 0; background: var(--bg); z-index: 10;
    }
    .logo { display: flex; align-items: center; gap: 10px; }
    .logo-icon { width: 28px; height: 28px; border-radius: 7px; background: var(--accent); display: grid; place-items: center; font-size: 14px; font-weight: 800; color: #fff; }
    .logo-text { font-size: 18px; font-weight: 800; letter-spacing: 3px; }
    .report-label { font-size: 11px; color: var(--accent2); letter-spacing: 1.5px; text-transform: uppercase; }

    .page { max-width: 720px; margin: 0 auto; padding: 36px 20px 60px; }

    /* ── Score ring ── */
    .score-wrap { display: flex; flex-direction: column; align-items: center; margin-bottom: 28px; animation: popIn .4s cubic-bezier(.34,1.56,.64,1) both; }
    @keyframes popIn { from{opacity:0;transform:scale(.7);} to{opacity:1;transform:scale(1);} }
    .score-ring {
      width: 110px; height: 110px; border-radius: 50%; border: 6px solid var(--accent2);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      background: #0A1520; margin-bottom: 10px;
      box-shadow: 0 0 32px rgba(79,195,247,.12);
    }
    .score-ring.good   { border-color: #2ECC71; box-shadow: 0 0 32px rgba(46,204,113,.15); }
    .score-ring.mid    { border-color: #F39C12; box-shadow: 0 0 32px rgba(243,156,18,.15); }
    .score-ring.low    { border-color: #E74C3C; box-shadow: 0 0 32px rgba(231,76,60,.15); }
    .score-num  { font-size: 36px; font-weight: 800; font-family: var(--mono); line-height: 1; }
    .score-den  { font-size: 11px; color: var(--muted); }
    .score-label { font-size: 13px; font-weight: 600; letter-spacing: .8px; }

    .case-title  { text-align: center; margin-bottom: 6px; }
    .case-title h2 { font-size: 22px; font-weight: 700; }
    .case-title p  { font-size: 13px; color: var(--accent2); margin-top: 3px; }

    /* ── Stats row ── */
    .stats { display: flex; justify-content: center; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; }
    .stat { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 10px 18px; text-align: center; min-width: 90px; }
    .stat .sv { font-size: 18px; font-weight: 700; font-family: var(--mono); }
    .stat .sl { font-size: 10px; color: var(--muted); margin-top: 2px; letter-spacing: .4px; }

    /* ── Summary block ── */
    .section-title { font-size: 10px; color: var(--accent2); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 12px; }
    .summary-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: 14px; padding: 20px; margin-bottom: 24px;
      animation: fadeUp .4s .1s ease both;
    }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }

    .summary-text { font-size: 14px; color: #A8C8E0; line-height: 1.65; white-space: pre-wrap; }
    /* Render bold headers */
    .sh { font-size: 14px; font-weight: 700; color: var(--text); margin: 14px 0 5px; display: block; }
    .sh:first-child { margin-top: 0; }
    .bullet { display: flex; gap: 8px; margin-bottom: 7px; font-size: 13px; color: #A0C4DE; line-height: 1.55; }
    .bullet-dot { color: var(--accent2); flex-shrink: 0; margin-top: 3px; font-size: 10px; }

    /* ── Actions ── */
    .actions { display: flex; gap: 12px; }
    .btn { flex: 1; padding: 13px; border-radius: 10px; border: none; font-size: 14px; font-weight: 600; cursor: pointer; font-family: var(--sans); text-align: center; text-decoration: none; display: grid; place-items: center; }
    .btn-sec  { background: var(--surface); border: 1px solid var(--border) !important; color: var(--muted); }
    .btn-sec:hover  { border-color: var(--accent) !important; color: var(--accent2); }
    .btn-prim { background: var(--accent); color: #fff; }
    .btn-prim:hover { background: #3AAAD8; }
  </style>
</head>
<body>
<nav class="topbar">
  <div class="logo">
    <div class="logo-icon">✚</div>
    <span class="logo-text">NUR-SIM</span>
  </div>
  <span class="report-label">Performance Report</span>
</nav>

<div class="page">
  <div class="case-title" id="case-title">
    <h2>—</h2><p>—</p>
  </div>

  <div class="score-wrap">
    <div class="score-ring" id="score-ring">
      <span class="score-num" id="score-num">—</span>
      <span class="score-den">/100</span>
    </div>
    <span class="score-label" id="score-label" style="color:var(--accent2)">—</span>
  </div>

  <div class="stats" id="stats-row"></div>

  <div class="section-title">Clinical Feedback</div>
  <div class="summary-card" id="summary-card">
    <div id="summary-content" style="color:var(--muted);font-size:14px;">Loading report…</div>
  </div>

  <div class="actions">
    <a href="index.php" class="btn btn-sec">← Case List</a>
    <button class="btn btn-prim" id="retry-btn">Retry Case →</button>
  </div>
</div>

<script>
const summaryData = JSON.parse(sessionStorage.getItem('nursim_summary') || 'null');
const caseObj     = JSON.parse(sessionStorage.getItem('nursim_case')    || 'null');

if (!summaryData || !caseObj) { window.location.href = '../dashboard.php'; }

// ── Case info ──────────────────────────────
document.querySelector('#case-title h2').textContent = caseObj.patient_name;
document.querySelector('#case-title p').textContent  = caseObj.diagnosis;

// ── Score ring ─────────────────────────────
const score = summaryData.score;
const ring  = document.getElementById('score-ring');
const numEl = document.getElementById('score-num');
const lblEl = document.getElementById('score-label');

if (score !== null && score !== undefined) {
  numEl.textContent = score;
  if (score >= 75) {
    ring.classList.add('good'); lblEl.textContent = 'Proficient';   lblEl.style.color = '#2ECC71';
  } else if (score >= 50) {
    ring.classList.add('mid');  lblEl.textContent = 'Developing';   lblEl.style.color = '#F39C12';
  } else {
    ring.classList.add('low');  lblEl.textContent = 'Needs Practice'; lblEl.style.color = '#E74C3C';
  }
} else {
  numEl.textContent = '—'; lblEl.textContent = 'Reviewed';
}

// ── Stats ──────────────────────────────────
const m = Math.floor((summaryData.elapsed || 0) / 60);
const s = (summaryData.elapsed || 0) % 60;
const stats = [
  { v: summaryData.message_count ?? '—', l: 'Messages' },
  { v: `${m}m ${s}s`,                    l: 'Duration'  },
  { v: caseObj.difficulty,               l: 'Difficulty' },
];
const statsRow = document.getElementById('stats-row');
stats.forEach(st => {
  const el = document.createElement('div');
  el.className = 'stat';
  el.innerHTML = `<div class="sv">${st.v}</div><div class="sl">${st.l}</div>`;
  statsRow.appendChild(el);
});

// ── Summary rendering ─────────────────────
function renderSummary(text) {
  const container = document.getElementById('summary-content');
  container.innerHTML = '';
  const lines = text.split('\n');

  lines.forEach(line => {
    const trimmed = line.trim();
    if (!trimmed) return;

    // Section headings: **text** or "1. Some Heading"
    if (/^\*\*.+\*\*$/.test(trimmed) || /^\d+\.\s/.test(trimmed)) {
      const sh = document.createElement('span');
      sh.className = 'sh';
      sh.textContent = trimmed.replace(/\*\*/g, '');
      container.appendChild(sh);
    // Bullet points
    } else if (/^[-•▸]/.test(trimmed)) {
      const row = document.createElement('div');
      row.className = 'bullet';
      row.innerHTML = `<span class="bullet-dot">▸</span><span>${escHtml(trimmed.replace(/^[-•▸]\s*/,''))}</span>`;
      container.appendChild(row);
    // Regular text
    } else {
      const p = document.createElement('p');
      p.style.cssText = 'font-size:13px;color:#8AACCA;line-height:1.65;margin-bottom:6px;';
      p.textContent = trimmed;
      container.appendChild(p);
    }
  });
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

renderSummary(summaryData.summary || 'No summary available.');

// ── Retry ──────────────────────────────────
document.getElementById('retry-btn').addEventListener('click', async () => {
  const API = '<?= defined("API_URL") ? API_URL : "http://localhost/Nur-Sim/PhP/api" ?>';
  const token = localStorage.getItem('authToken');
  if (!token) { window.location.href = '../login.php'; return; }
  
  try {
    const res  = await fetch(`${API}/sessions/start`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({ case_id: caseObj.id }),
    });
    const data = await res.json();
    sessionStorage.setItem('currentSession', JSON.stringify(data));
    window.location.href = 'simulation.php';
  } catch (e) {
    alert('Failed to start new session.');
  }
});
</script>
</body>
</html>
