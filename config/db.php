<?php

require_once __DIR__ . '/config.php';

/**
 * Shared PDO connection — CLAUDE.md Rule 3: every query goes through this,
 * prepared statements only, never string-concatenated SQL.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env('DB_HOST', 'localhost');
    $name = env('DB_NAME', 'gu_website');
    $user = env('DB_USER', 'root');
    $pass = env('DB_PASS', '');
    $charset = env('DB_CHARSET', 'utf8mb4');

    $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        error_log('DB connection failed: ' . $e->getMessage());
        if (APP_ENV === 'development') {
            throw $e;
        }
        http_response_code(500);
        exit('The site is temporarily unavailable. Please try again shortly.');
    }

    return $pdo;
}
