<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nur-Sim — Simulation Room</title>
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

    /* ── Layout ── */
    .layout { display: flex; flex-direction: column; height: 100vh; }

    /* ── Topbar ── */
    .topbar {
      display: flex; align-items: center; gap: 12px;
      padding: 10px 16px; border-bottom: 1px solid var(--border);
      background: var(--bg); flex-shrink: 0;
    }
    .logo-icon { width: 26px; height: 26px; border-radius: 6px; background: var(--accent); display: grid; place-items: center; font-size: 13px; font-weight: 800; color: #fff; flex-shrink: 0; }
    .logo-text { font-size: 16px; font-weight: 800; letter-spacing: 2px; }
    .sep { color: var(--border); }
    .patient-info { flex: 1; }
    .patient-info strong { font-size: 15px; font-weight: 700; display: block; }
    .patient-info small  { font-size: 11px; color: var(--accent2); }
    .timer-box { font-family: var(--mono); font-size: 13px; color: var(--muted); min-width: 42px; text-align: right; }
    .vitals-toggle { background: var(--surface2); border: 1px solid var(--border); color: var(--muted); font-size: 12px; padding: 5px 10px; border-radius: 7px; cursor: pointer; font-family: var(--sans); transition: border-color .15s; white-space: nowrap; }
    .vitals-toggle:hover { border-color: var(--accent); color: var(--accent2); }
    .speaker-btn {
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--muted);
      font-size: 12px;
      padding: 5px 8px;
      border-radius: 7px;
      cursor: pointer;
      font-family: var(--sans);
      transition: border-color .15s, color .15s;
      margin-left: 8px;
    }
    .speaker-btn:hover { border-color: var(--accent); color: var(--accent2); }
    .speaker-btn:active { transform: scale(.95); }
    .end-btn { background: #2A0A0A; border: 1px solid #6B1A1A; color: #FF6B6B; font-size: 11px; font-weight: 700; letter-spacing: 1px; padding: 6px 13px; border-radius: 8px; cursor: pointer; font-family: var(--mono); transition: background .15s; white-space: nowrap; }
    .end-btn:hover { background: #3D0A0A; }

    /* ── Vitals Panel ── */
    .vitals-panel {
      background: #0A1A28; border-bottom: 1px solid var(--border);
      padding: 10px 16px; flex-shrink: 0;
      animation: slideDown .2s ease;
    }
    @keyframes slideDown { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }
    .vitals-label { font-size: 9px; color: var(--accent2); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px; }
    .vitals-row   { display: flex; gap: 8px; flex-wrap: wrap; }
    .vital { background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 7px 12px; min-width: 72px; }
    .vital.alert { border-color: #6B1A1A; background: #180808; }
    .vital .vl { font-size: 9px; color: var(--muted); letter-spacing: .8px; margin-bottom: 2px; }
    .vital .vv { font-size: 15px; font-weight: 700; color: var(--text); font-family: var(--mono); }
    .vital .vu { font-size: 9px; color: var(--muted); }
    .vital.alert .vv { color: #FF6B6B; }

    /* ── Quick Prompts ── */
    .quick-bar {
      display: flex; gap: 6px; padding: 6px 12px; flex-shrink: 0;
      background: #090F18; border-bottom: 1px solid var(--border); overflow-x: auto;
    }
    .quick-bar::-webkit-scrollbar { height: 0; }
    .quick-chip {
      background: var(--surface); border: 1px solid var(--border); color: var(--muted);
      font-size: 11px; padding: 5px 11px; border-radius: 20px; cursor: pointer;
      white-space: nowrap; font-family: var(--sans); transition: border-color .15s, color .15s;
    }
    .quick-chip:hover { border-color: var(--accent); color: var(--accent2); }

    /* ── Chat area ── */
    .chat-area { flex: 1; overflow-y: auto; padding: 16px 12px; display: flex; flex-direction: column; gap: 10px; scroll-behavior: smooth; }
    .chat-area::-webkit-scrollbar { width: 4px; }
    .chat-area::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .msg { display: flex; gap: 8px; max-width: 78%; animation: msgIn .2s ease; }
    @keyframes msgIn { from { opacity:0; transform: translateY(6px); } to { opacity:1; transform:translateY(0); } }
    .msg.user  { align-self: flex-end; flex-direction: row-reverse; }
    .msg.bot   { align-self: flex-start; }

    .avatar {
      width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
      background: #1A3A5C; border: 1px solid #2A5A8A;
      display: grid; place-items: center; font-size: 13px; font-weight: 700; color: var(--accent2);
    }
    .msg.user .avatar { background: #1A3A2E; border-color: #2A6A4A; color: #7BCCA4; }

    .bubble { padding: 10px 14px; border-radius: 14px; line-height: 1.55; font-size: 14px; }
    .msg.user .bubble { background: var(--accent); color: #fff; border-bottom-right-radius: 4px; }
    .msg.bot  .bubble { background: var(--surface2); color: var(--text); border-bottom-left-radius: 4px; }

    .msg-time { font-size: 10px; color: var(--muted); margin-top: 3px; font-family: var(--mono); }
    .msg.user .msg-time { text-align: right; }

    .typing-dots { display: flex; gap: 4px; align-items: center; padding: 12px 14px; }
    .typing-dots span { width: 6px; height: 6px; background: var(--muted); border-radius: 50%; animation: dot .9s infinite; }
    .typing-dots span:nth-child(2) { animation-delay: .15s; }
    .typing-dots span:nth-child(3) { animation-delay: .3s; }
    @keyframes dot { 0%,60%,100%{transform:translateY(0);opacity:.4;} 30%{transform:translateY(-5px);opacity:1;} }

    /* ── Input toolbar ── */
    .input-area {
      flex-shrink: 0; display: flex; gap: 8px; align-items: flex-end;
      padding: 10px 12px; border-top: 1px solid var(--border); background: #090F18;
    }
    .msg-input {
      flex: 1; background: var(--surface); border: 1px solid var(--border);
      color: var(--text); font-size: 14px; font-family: var(--sans);
      padding: 10px 14px; border-radius: 22px; resize: none; max-height: 100px;
      outline: none; transition: border-color .15s; line-height: 1.5;
    }
    .msg-input::placeholder { color: var(--muted); }
    .msg-input:focus { border-color: var(--accent); }
    .send-btn {
      width: 40px; height: 40px; border-radius: 50%; background: var(--accent);
      border: none; color: #fff; font-size: 18px; cursor: pointer;
      display: grid; place-items: center; flex-shrink: 0;
      transition: background .15s, transform .1s;
    }
    .send-btn:hover  { background: #3AAAD8; }
    .send-btn:active { transform: scale(.93); }
    .send-btn:disabled { background: var(--border); cursor: default; }

    /* ── Modal ── */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.65); z-index: 100; display: grid; place-items: center; animation: fadeIn .15s ease; }
    @keyframes fadeIn { from{opacity:0;} to{opacity:1;} }
    .modal { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 28px; max-width: 360px; width: 90%; text-align: center; }
    .modal h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
    .modal p  { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 20px; }
    .modal-btns { display: flex; gap: 10px; }
    .modal-btns button { flex: 1; padding: 11px; border-radius: 10px; border: none; font-size: 14px; font-weight: 600; cursor: pointer; font-family: var(--sans); }
    .btn-cancel { background: var(--surface2); border: 1px solid var(--border) !important; color: var(--muted); }
    .btn-confirm { background: var(--danger); color: #fff; }
  </style>
</head>
<body>
<div class="layout" id="app">
  <nav class="topbar">
    <div class="logo-icon">✚</div>
    <span class="logo-text">NUR-SIM</span>
    <span class="sep">|</span>
    <div class="patient-info">
      <strong id="tb-name">Loading…</strong>
      <small id="tb-diag"></small>
    </div>
    <span class="timer-box" id="timer">00:00</span>
    <button class="vitals-toggle" id="vitals-toggle"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
</svg><br> Vitals</button>
    <button class="speaker-btn" id="speaker-btn" title="Replay patient announcement">🔊</button>
    <button class="end-btn"       id="end-btn">END SIM</button>
  </nav>

  <div class="vitals-panel" id="vitals-panel" style="display:none">
    <div class="vitals-label">Current Vitals</div>
    <div class="vitals-row" id="vitals-row"></div>
  </div>

  <div class="quick-bar" id="quick-bar"></div>

  <div class="chat-area" id="chat-area"></div>

  <div class="input-area">
    <textarea class="msg-input" id="msg-input" rows="1" placeholder="Speak to your patient…"></textarea>
    <button class="send-btn" id="send-btn">↑</button>
  </div>
</div>

<!-- End Simulation Modal -->
<div class="modal-overlay" id="modal" style="display:none">
  <div class="modal">
    <h3>End Simulation?</h3>
    <p id="modal-body">Are you ready to receive your performance report?</p>
    <div class="modal-btns">
      <button class="btn-cancel"  id="modal-cancel">Continue</button>
      <button class="btn-confirm" id="modal-confirm">End & Get Report</button>
    </div>
  </div>
</div>

<script>
const API     = '<?= defined("API_URL") ? API_URL : "http://localhost/Nur-Sim/PhP/api" ?>';
const session = JSON.parse(sessionStorage.getItem('currentSession') || 'null');
const token   = localStorage.getItem('authToken');

if (!session || !token) { window.location.href = '../login.php'; }

const caseObj = session.case;

// ── State ──────────────────────────────────
let elapsed  = 0;
let msgCount = 0;
let isTyping = false;

// ── DOM refs ──────────────────────────────
const chatArea    = document.getElementById('chat-area');
const msgInput    = document.getElementById('msg-input');
const sendBtn     = document.getElementById('send-btn');
const timerEl     = document.getElementById('timer');
const vitalsPanel = document.getElementById('vitals-panel');
const vitalsRow   = document.getElementById('vitals-row');
const quickBar    = document.getElementById('quick-bar');
const modal       = document.getElementById('modal');
const modalBody   = document.getElementById('modal-body');

// ── Init ───────────────────────────────────
document.getElementById('tb-name').textContent = `${caseObj.patient_name}, ${caseObj.age}`;
document.getElementById('tb-diag').textContent = caseObj.diagnosis;

// Timer
setInterval(() => {
  elapsed++;
  const m = String(Math.floor(elapsed / 60)).padStart(2,'0');
  const s = String(elapsed % 60).padStart(2,'0');
  timerEl.textContent = `${m}:${s}`;
}, 1000);

// Vitals data
const vitalsData = [];
if (caseObj.bp)   vitalsData.push({ l:'BP',   v:caseObj.bp,   u:'mmHg', alert:false });
if (caseObj.hr)   vitalsData.push({ l:'HR',   v:caseObj.hr,   u:'bpm',  alert:false });
if (caseObj.rr)   vitalsData.push({ l:'RR',   v:caseObj.rr,   u:'/min', alert:false });
if (caseObj.spo2) vitalsData.push({ l:'SpO₂', v:caseObj.spo2, u:'%',    alert:false });
if (caseObj.temp) vitalsData.push({ l:'Temp', v:caseObj.temp, u:'°C',   alert:false });

vitalsData.forEach(v => {
  const el = document.createElement('div');
  el.className = 'vital' + (v.alert ? ' alert' : '');
  el.innerHTML = `<div class="vl">${v.l}</div><div class="vv">${v.v}<span class="vu"> ${v.u}</span></div>`;
  vitalsRow.appendChild(el);
});

// Vitals toggle
document.getElementById('vitals-toggle').addEventListener('click', () => {
  vitalsPanel.style.display = vitalsPanel.style.display === 'none' ? 'block' : 'none';
});

// Speaker button
document.getElementById('speaker-btn').addEventListener('click', () => {
  announcePatientSituation();
});

// Announce patient situation on load
setTimeout(() => announcePatientSituation(), 1000); // Small delay to ensure voices are loaded

// Quick prompts
const PROMPTS = [
  "Hello, I'm your nurse. How are you feeling right now?",
  "Can you rate your pain from 0 to 10?",
  "Where exactly does it hurt?",
  "Are you allergic to any medications?",
  "What medications do you currently take?",
  "When did this start?",
];
PROMPTS.forEach(p => {
  const btn = document.createElement('button');
  btn.className = 'quick-chip';
  btn.textContent = p;
  btn.addEventListener('click', () => sendMessage(p));
  quickBar.appendChild(btn);
});

// ── Chat helpers ──────────────────────────
function nowTime() {
  return new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
}

function appendMsg(role, text) {
  const isUser = role === 'user';
  const initial = isUser ? 'You' : caseObj.patient_name[0];

  const wrap = document.createElement('div');
  wrap.className = `msg ${isUser ? 'user' : 'bot'}`;
  wrap.innerHTML = `
    <div class="avatar">${initial}</div>
    <div>
      <div class="bubble">${escHtml(text)}</div>
      <div class="msg-time">${nowTime()}</div>
    </div>`;
  chatArea.appendChild(wrap);
  chatArea.scrollTop = chatArea.scrollHeight;
}

function showTyping() {
  const el = document.createElement('div');
  el.className = 'msg bot'; el.id = 'typing';
  el.innerHTML = `<div class="avatar">${caseObj.patient_name[0]}</div>
    <div class="bubble typing-dots"><span></span><span></span><span></span></div>`;
  chatArea.appendChild(el);
  chatArea.scrollTop = chatArea.scrollHeight;
}
function hideTyping() {
  document.getElementById('typing')?.remove();
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

// ── Send message ───────────────────────────
async function sendMessage(text) {
  text = (text || msgInput.value).trim();
  if (!text || isTyping) return;

  msgInput.value = '';
  msgInput.style.height = 'auto';
  appendMsg('user', text);
  msgCount++;

  isTyping = true;
  sendBtn.disabled = true;
  showTyping();

  try {
    const res  = await fetch(`${API}/chat`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({ session_id: session.session_id, message: text }),
    });
    const data = await res.json();
    hideTyping();
    appendMsg('bot', data.reply || data.error || 'No response.');
  } catch (e) {
    hideTyping();
    appendMsg('bot', '⚠️ Connection error. Please check the server is running.');
  } finally {
    isTyping = false;
    sendBtn.disabled = false;
  }
}

sendBtn.addEventListener('click', () => sendMessage());
msgInput.addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});
msgInput.addEventListener('input', () => {
  msgInput.style.height = 'auto';
  msgInput.style.height = Math.min(msgInput.scrollHeight, 100) + 'px';
});

// ── End session ────────────────────────────
document.getElementById('end-btn').addEventListener('click', () => {
  const m = Math.floor(elapsed / 60), s = elapsed % 60;
  modalBody.textContent = `You sent ${msgCount} messages over ${m}m ${s}s. Ready for your performance report?`;
  modal.style.display = 'grid';
});
document.getElementById('modal-cancel').addEventListener('click', () => { modal.style.display = 'none'; });
document.getElementById('modal-confirm').addEventListener('click', async () => {
  modal.style.display = 'none';
  appendMsg('bot', '⏳ Generating your performance report, please wait…');
  try {
    const res  = await fetch(`${API}/sessions/end`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({ session_id: session.session_id }),
    });
    const data = await res.json();
    sessionStorage.setItem('nursim_summary', JSON.stringify({ ...data, elapsed }));
    window.location.href = 'summary.php';
  } catch (e) {
    appendMsg('bot', '⚠️ Could not end session. Please try again.');
  }
});

// ── Text-to-Speech ───────────────────────  
function speakText(text) {
  if ('speechSynthesis' in window) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.rate = 0.9; // Slightly slower for clarity
    utterance.pitch = 1;
    utterance.volume = 0.8;

    // Use a female voice if available (common for medical contexts)
    const voices = speechSynthesis.getVoices();
    const femaleVoice = voices.find(voice =>
      voice.name.toLowerCase().includes('female') ||
      voice.name.toLowerCase().includes('woman') ||
      voice.name.toLowerCase().includes('karen') ||
      voice.name.toLowerCase().includes('samantha') ||
      voice.name.toLowerCase().includes('zira')
    );
    if (femaleVoice) {
      utterance.voice = femaleVoice;
    }

    speechSynthesis.speak(utterance);
  }
}

function announcePatientSituation() {
  const vitalsText = vitalsData.map(v => `${v.l} is ${v.v} ${v.u}`).join(', ');
  const announcement = `Patient ${caseObj.patient_name}, ${caseObj.age} years old, diagnosed with ${caseObj.diagnosis}. ${vitalsText ? `Current vitals: ${vitalsText}.` : ''} Beginning simulation.`;

  speakText(announcement);
}
</script>
</body>
</html>
