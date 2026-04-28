<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nur-Sim — Admin: Add Case</title>
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

    .admin-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; max-width: 1200px; margin: 0 auto; }

    .form-section { }
    .preview-section { padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; }
    .preview-section h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--accent2); }
    .preview-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); margin-bottom: 20px; }
    .preview-info { font-size: 14px; line-height: 1.8; color: var(--muted); }
    .preview-info strong { color: var(--text); display: block; margin-top: 10px; }

    .header { text-align: center; margin-bottom: 40px; }
    .header h1 { font-size: 32px; font-weight: 800; margin-bottom: 8px; }
    .header p { color: var(--muted); font-size: 16px; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--accent2); }
    .form-group input, .form-group textarea, .form-group select {
      width: 100%; background: var(--surface); border: 1px solid var(--border); color: var(--text);
      font-size: 14px; font-family: var(--sans); padding: 12px; border-radius: 8px; outline: none;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--accent); }
    .form-group textarea { resize: vertical; min-height: 100px; }

    .vitals-row { display: flex; gap: 8px; flex-wrap: wrap; }
    .vitals-row input { flex: 1; min-width: 80px; }

    .tags-input { display: flex; gap: 8px; flex-wrap: wrap; }
    .tag { background: var(--accent); color: #fff; padding: 4px 8px; border-radius: 12px; font-size: 12px; }
    .tag input { background: transparent; border: none; color: #fff; width: 60px; outline: none; }

    .btn { background: var(--accent); border: none; color: #fff; font-size: 16px; font-weight: 600; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-family: var(--sans); }
    .btn:hover { background: #3AAAD8; }
    .btn:disabled { background: var(--border); cursor: not-allowed; }

    .message { margin-top: 20px; padding: 12px; border-radius: 8px; }
    .message.success { background: #1A3A2E; border: 1px solid #2A6A4A; color: #7BCCA4; }
    .message.error { background: #3D0A0A; border: 1px solid #6B1A1A; color: #FF6B6B; }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="sidebar-header">
        <h2>Nur-Sim</h2>
      </div>
      <nav class="sidebar-nav">
        <a href="index.php" class="nav-link">Case Selection</a>
        <a href="admin.php" class="nav-link active">Admin Panel</a>
        <a href="#" class="nav-link">Reports</a>
        <a href="#" class="nav-link">Settings</a>
      </nav>
    </div>

    <div class="main-content">
      <div class="header">
        <h1>Admin Panel</h1>
        <p>Add a new patient case</p>
      </div>

      <div class="admin-wrapper">
        <div class="form-section">
      <form id="case-form" enctype="multipart/form-data">
      <div class="form-group">
        <label for="id">Case ID (unique)</label>
        <input type="text" id="id" required>
      </div>

      <div class="form-group">
        <label for="title">Case Title</label>
        <input type="text" id="title" required>
      </div>

      <div class="form-group">
        <label for="patient_name">Patient Name</label>
        <input type="text" id="patient_name" required>
      </div>

      <div class="form-group">
        <label for="age">Age</label>
        <input type="number" id="age" required>
      </div>

      <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" required>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>

      <div class="form-group">
        <label for="diagnosis">Diagnosis</label>
        <input type="text" id="diagnosis" required>
      </div>

      <div class="form-group">
        <label for="difficulty">Difficulty</label>
        <select id="difficulty" required>
          <option value="Beginner">Beginner</option>
          <option value="Intermediate">Intermediate</option>
          <option value="Advanced">Advanced</option>
        </select>
      </div>

      <div class="form-group">
        <label for="tags">Tags (comma-separated)</label>
        <input type="text" id="tags" placeholder="e.g., Cardiovascular, Emergency">
      </div>

      <div class="form-group">
        <label for="system_prompt">System Prompt</label>
        <textarea id="system_prompt" required placeholder="Detailed patient persona and clinical instructions..."></textarea>
      </div>

      <div class="form-group">
        <label for="profile_image">Profile Image (upload)</label>
        <input type="file" id="profile_image" accept="image/*">
      </div>

      <div class="form-group">
        <label>Vitals (optional)</label>
        <div class="vitals-row">
          <input type="text" id="bp" placeholder="BP (e.g., 120/80)">
          <input type="text" id="hr" placeholder="HR (e.g., 72)">
          <input type="text" id="rr" placeholder="RR (e.g., 16)">
          <input type="text" id="temp" placeholder="Temp (e.g., 36.8)">
          <input type="text" id="spo2" placeholder="SpO2 (e.g., 98%)">
        </div>
      </div>

      <button type="submit" class="btn" id="submit-btn">Add Case</button>
    </form>
        </div>

        <div class="preview-section">
          <h3>Preview</h3>
          <img id="preview-avatar" class="preview-avatar" src="https://via.placeholder.com/120x120?text=Avatar" alt="Preview">
          <div class="preview-info">
            <strong id="preview-name">Patient Name</strong>
            <div id="preview-patient">Age: -- | Gender: --</div>
            <strong style="margin-top: 15px;">Diagnosis</strong>
            <div id="preview-diagnosis">--</div>
            <strong style="margin-top: 15px;">Difficulty</strong>
            <div id="preview-difficulty">--</div>
            <strong style="margin-top: 15px;">Vitals</strong>
            <div id="preview-vitals">--</div>
          </div>
        </div>
      </div>

    <div id="message"></div>
  </div>

  <script>
    const API_URL = '<?= defined("API_URL") ? API_URL : "http://localhost/Nur-Sim/PhP/api" ?>';

    // Update preview in real-time
    const updatePreview = () => {
      const patient_name = document.getElementById('patient_name').value || 'Patient Name';
      const age = document.getElementById('age').value || '--';
      const gender = document.getElementById('gender').value || '--';
      const diagnosis = document.getElementById('diagnosis').value || '--';
      const difficulty = document.getElementById('difficulty').value || '--';
      const bp = document.getElementById('bp').value || '--';
      const hr = document.getElementById('hr').value || '--';
      const rr = document.getElementById('rr').value || '--';
      const temp = document.getElementById('temp').value || '--';
      const spo2 = document.getElementById('spo2').value || '--';

      document.getElementById('preview-name').textContent = patient_name;
      document.getElementById('preview-patient').textContent = `Age: ${age} | Gender: ${gender}`;
      document.getElementById('preview-diagnosis').textContent = diagnosis;
      document.getElementById('preview-difficulty').textContent = difficulty;
      
      const vitals = [bp, hr, rr, temp, spo2].filter(v => v !== '--').join(' | ') || '--';
      document.getElementById('preview-vitals').textContent = vitals;
    };

    // Preview image
    document.getElementById('profile_image').addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
          document.getElementById('preview-avatar').src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    // Update preview on input change
    ['patient_name', 'age', 'gender', 'diagnosis', 'difficulty', 'bp', 'hr', 'rr', 'temp', 'spo2'].forEach(id => {
      document.getElementById(id).addEventListener('change', updatePreview);
      document.getElementById(id).addEventListener('input', updatePreview);
    });

    document.getElementById('case-form').addEventListener('submit', async (e) => {
      e.preventDefault();

      const btn = document.getElementById('submit-btn');
      btn.disabled = true;
      btn.textContent = 'Adding...';

      const formData = new FormData();
      formData.append('id', document.getElementById('id').value);
      formData.append('title', document.getElementById('title').value);
      formData.append('patient_name', document.getElementById('patient_name').value);
      formData.append('age', document.getElementById('age').value);
      formData.append('gender', document.getElementById('gender').value);
      formData.append('diagnosis', document.getElementById('diagnosis').value);
      formData.append('difficulty', document.getElementById('difficulty').value);
      formData.append('tags', document.getElementById('tags').value);
      formData.append('system_prompt', document.getElementById('system_prompt').value);

      const imageFile = document.getElementById('profile_image').files[0];
      if (imageFile) {
        formData.append('profile_image', imageFile);
      }

      formData.append('bp', document.getElementById('bp').value);
      formData.append('hr', document.getElementById('hr').value);
      formData.append('rr', document.getElementById('rr').value);
      formData.append('temp', document.getElementById('temp').value);
      formData.append('spo2', document.getElementById('spo2').value);

      try {
        const res = await fetch(`${API_URL}/admin/add-case`, {
          method: 'POST',
          body: formData
        });

        const result = await res.json();

        if (res.ok) {
          document.getElementById('message').innerHTML = '<div class="message success">Case added successfully!</div>';
          document.getElementById('case-form').reset();
        } else {
          document.getElementById('message').innerHTML = `<div class="message error">${result.error || 'Error adding case'}</div>`;
        }
      } catch (err) {
        document.getElementById('message').innerHTML = '<div class="message error">Network error</div>';
        console.error(err);
      } finally {
        btn.disabled = false;
        btn.textContent = 'Add Case';
      }
    });
  </script>
</body>
</html>