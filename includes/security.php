<?php

require_once __DIR__ . '/../config/config.php';

/**
 * Escape for HTML output — CLAUDE.md Rule 5: never echo raw input,
 * user-submitted or CMS-authored, without this.
 */
function esc(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Secure session start — never call session_start() directly (Rule 5).
 */
function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(SESSION_COOKIE_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => APP_ENV !== 'development',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    $timeout = 30 * 60; // 30 minutes inactivity timeout
    if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity']) > $timeout) {
        destroy_session();
        session_start();
    }
    $_SESSION['_last_activity'] = time();
}

function destroy_session(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        session_destroy();
    }
}

function regenerate_session(): void
{
    start_secure_session();
    session_regenerate_id(true);
}

/**
 * CSRF token helpers — every state-changing form uses csrf_field(),
 * every handler calls verify_csrf() before touching the database (Rule 5).
 */
function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    start_secure_session();
    $submitted = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['_csrf_token'] ?? '';
    if ($expected === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        exit('Your session expired or the form was resubmitted. Please go back and try again.');
    }
    return true;
}
