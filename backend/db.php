<?php
// db.php — SQLite connection + schema init

declare(strict_types=1);

function get_db(): PDO {
    static $db = null;
    if ($db !== null) return $db;

    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $db->exec("PRAGMA journal_mode=WAL;");

    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id                          TEXT PRIMARY KEY,
            email                       TEXT UNIQUE NOT NULL,
            password_hash               TEXT NOT NULL,
            first_name                  TEXT NOT NULL,
            last_name                   TEXT NOT NULL,
            email_verified              INTEGER DEFAULT 0,
            email_verification_token    TEXT,
            email_verification_expires  TEXT,
            created_at                  TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at                  TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS user_sessions (
            id         TEXT PRIMARY KEY,
            user_id    TEXT NOT NULL,
            token      TEXT UNIQUE NOT NULL,
            expires_at TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cases (
            id            TEXT PRIMARY KEY,
            title         TEXT NOT NULL,
            patient_name  TEXT NOT NULL,
            age           INTEGER NOT NULL,
            gender        TEXT NOT NULL,
            diagnosis     TEXT NOT NULL,
            difficulty    TEXT NOT NULL,
            tags          TEXT NOT NULL,  -- JSON array
            system_prompt TEXT NOT NULL,
            profile_image TEXT,  -- URL or path to profile image            bp            TEXT,
            hr            TEXT,
            rr            TEXT,
            temp          TEXT,
            spo2          TEXT,            created_at    TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS simulation_sessions (
            id            TEXT PRIMARY KEY,
            user_id       TEXT,
            case_id       TEXT NOT NULL,
            student_id    TEXT,
            started_at    TEXT NOT NULL,
            ended_at      TEXT,
            status        TEXT DEFAULT 'active',
            message_count INTEGER DEFAULT 0,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS performance_logs (
            id         TEXT PRIMARY KEY,
            session_id TEXT NOT NULL,
            timestamp  TEXT NOT NULL,
            role       TEXT NOT NULL,
            content    TEXT NOT NULL,
            FOREIGN KEY (session_id) REFERENCES simulation_sessions(id)
        );

        CREATE TABLE IF NOT EXISTS session_summaries (
            id         TEXT PRIMARY KEY,
            session_id TEXT NOT NULL UNIQUE,
            summary    TEXT NOT NULL,
            score      INTEGER,
            created_at TEXT NOT NULL,
            FOREIGN KEY (session_id) REFERENCES simulation_sessions(id)
        );
    ");

    // Add profile_image column if it doesn't exist
    try {
        $db->exec("ALTER TABLE cases ADD COLUMN profile_image TEXT;");
    } catch (Exception $e) {
        // Column might already exist, ignore
    }

    // Add vitals columns if they don't exist
    try {
        $db->exec("ALTER TABLE cases ADD COLUMN bp TEXT;");
    } catch (Exception $e) {}
    
    try {
        $db->exec("ALTER TABLE cases ADD COLUMN hr TEXT;");
    } catch (Exception $e) {}
    
    try {
        $db->exec("ALTER TABLE cases ADD COLUMN rr TEXT;");
    } catch (Exception $e) {}
    
    try {
        $db->exec("ALTER TABLE cases ADD COLUMN temp TEXT;");
    } catch (Exception $e) {}
    
    try {
        $db->exec("ALTER TABLE cases ADD COLUMN spo2 TEXT;");
    } catch (Exception $e) {}

    // Add email verification columns if they don't exist
    try {
        $db->exec("ALTER TABLE users ADD COLUMN email_verified INTEGER DEFAULT 0;");
    } catch (Exception $e) {}
    
    try {
        $db->exec("ALTER TABLE users ADD COLUMN email_verification_token TEXT;");
    } catch (Exception $e) {}
    
    try {
        $db->exec("ALTER TABLE users ADD COLUMN email_verification_expires TEXT;");
    } catch (Exception $e) {}

    return $db;
}
