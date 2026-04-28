<?php
// ─────────────────────────────────────────────
// config.php — Load .env and define constants
// ─────────────────────────────────────────────

// Simple .env loader (no composer needed)
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
        putenv(trim($key) . '=' . trim($val));
    }
}

define('AI_PROVIDER',       $_ENV['AI_PROVIDER']       ?? 'openai');
define('ANTHROPIC_API_KEY', $_ENV['ANTHROPIC_API_KEY'] ?? '');
define('OPENAI_API_KEY',    $_ENV['OPENAI_API_KEY']    ?? '');
define('GEMINI_API_KEY',    $_ENV['GEMINI_API_KEY']    ?? '');
define('GEMINI_MODEL',      $_ENV['GEMINI_MODEL']      ?? 'gemini-3-flash-preview');
define('DB_PATH',           $_ENV['DB_PATH']           ?? __DIR__ . '/nursim.db');

// Email configuration
define('SMTP_HOST',         $_ENV['SMTP_HOST']         ?? 'smtp.gmail.com');
define('SMTP_PORT',         $_ENV['SMTP_PORT']         ?? 587);
define('SMTP_USERNAME',     $_ENV['SMTP_USERNAME']     ?? '');
define('SMTP_PASSWORD',     $_ENV['SMTP_PASSWORD']     ?? '');
define('SMTP_ENCRYPTION',   $_ENV['SMTP_ENCRYPTION']   ?? 'tls');
define('FROM_EMAIL',        $_ENV['FROM_EMAIL']        ?? 'noreply@nur-sim.com');
define('FROM_NAME',         $_ENV['FROM_NAME']         ?? 'Nur-Sim');
