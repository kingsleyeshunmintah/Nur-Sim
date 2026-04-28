# Nur-Sim PHP Edition

AI-powered nursing simulation platform built entirely in **plain PHP** (no framework) with a vanilla JS + PHP frontend.

---

## Project Structure

```
nur-sim-php/
│
├── backend/            ← PHP REST API
│   ├── index.php       ← Router + all route handlers
│   ├── config.php      ← .env loader + constants
│   ├── db.php          ← SQLite PDO connection + schema
│   ├── ai.php          ← Anthropic + OpenAI API callers (cURL)
│   ├── cases.php       ← All 4 patient case definitions
│   ├── .htaccess       ← Apache URL rewriting
│   └── .env            ← ← YOU CREATE THIS (see below)
│
└── frontend/           ← PHP web frontend (no React needed)
    ├── index.php       ← Case Selection screen
    ├── simulation.php  ← Chat simulation room
    └── summary.php     ← Performance report
```

---

## Requirements

- PHP 8.1+ with extensions: `pdo`, `pdo_sqlite`, `curl`, `json`
- A web server: **Apache** (with mod_rewrite) or use PHP's built-in server for dev
- An **Anthropic** or **OpenAI** API key

---

## Backend Setup

### 1. Create your `.env` file

Inside `backend/`, create a file named `.env`:

```env
# Choose one provider
AI_PROVIDER=anthropic
ANTHROPIC_API_KEY=sk-ant-your-key-here

# OR use OpenAI
# AI_PROVIDER=openai
# OPENAI_API_KEY=sk-your-openai-key-here

# Optional: change DB path
DB_PATH=/path/to/nursim.db
```

### 2. Run the backend (development)

```bash
cd nur-sim-php/backend
php -S localhost:8000
```

The SQLite database (`nursim.db`) is created automatically on first request.

### 3. Production (Apache)

Point your Apache vhost `DocumentRoot` to `nur-sim-php/backend/` and ensure `mod_rewrite` is enabled. The `.htaccess` routes all requests to `index.php`.

---

## Frontend Setup

### Development (PHP built-in server)

```bash
cd nur-sim-php/frontend
php -S localhost:3000
```

Open `http://localhost:3000` in your browser.

> **Note:** The frontend calls the API at `http://localhost:8000` by default.
> If deploying to a server, define `API_URL` before including files, or edit the `API` variable directly in each `.php` file.

### Production (Apache)

Point a second vhost (or subdirectory) to `nur-sim-php/frontend/`.
To set the API URL for production, define the constant at the top of each frontend file:

```php
<?php define('API_URL', 'https://your-api-domain.com'); ?>
```

---

## API Reference

| Method | Endpoint              | Description                        |
|--------|-----------------------|------------------------------------|
| GET    | `/`                   | Health check                       |
| GET    | `/cases`              | List all patient cases             |
| POST   | `/sessions/start`     | Start a simulation session         |
| POST   | `/chat`               | Send message to AI patient         |
| POST   | `/sessions/end`       | End session + get AI performance report |
| GET    | `/sessions/{id}`      | Get session + full message history |

### Example: Start a session

```bash
curl -X POST http://localhost:8000/sessions/start \
  -H "Content-Type: application/json" \
  -d '{"case_id":"hypertension","student_id":"student-001"}'
```

### Example: Send a chat message

```bash
curl -X POST http://localhost:8000/chat \
  -H "Content-Type: application/json" \
  -d '{"session_id":"<id-from-above>","message":"Hello, I am your nurse. How are you feeling?"}'
```

---

## Patient Cases

| ID           | Patient      | Diagnosis                    | Difficulty   |
|--------------|--------------|------------------------------|--------------|
| hypertension | Abena, 58    | Hypertensive Crisis          | Intermediate |
| postop       | Kofi, 45     | Post-Op Day 1 (Appendectomy) | Beginner     |
| dka          | Ama, 22      | Diabetic Ketoacidosis        | Advanced     |
| chest_pain   | Emmanuel, 62 | Suspected NSTEMI             | Advanced     |

---

## Adding a New Case

In `backend/cases.php`, add a new entry to the array returned by `get_cases()`:

```php
'my_case' => [
    'id'            => 'my_case',
    'title'         => 'Case Title',
    'patient_name'  => 'Patient Name',
    'age'           => 40,
    'gender'        => 'female',
    'diagnosis'     => 'Clinical Diagnosis',
    'difficulty'    => 'Beginner',   // Beginner | Intermediate | Advanced
    'tags'          => ['Tag1', 'Tag2'],
    'system_prompt' => <<<PROMPT
You are [Name], a [age]-year-old [background]...
[Full patient persona and safety rules here]
PROMPT,
],
```

Then add matching vitals in `frontend/simulation.php` inside the `VITALS` JS object.

---

## Security Notes for Production

- Move `nursim.db` outside the web root and update `DB_PATH` in `.env`
- Use parameterised PDO queries (already done in session insert; extend for all queries)
- Restrict `Access-Control-Allow-Origin` to your frontend domain
- Store `.env` outside the web root or protect it with `.htaccess`
- Rate-limit the `/chat` endpoint to prevent API cost abuse
