<?php

/**
 * Tiny dependency-free .env loader — no Composer dependencies by design
 * (CLAUDE.md Rule 5), mirroring gu-services' own stack choice.
 */
function gu_load_env(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (strlen($value) >= 2 && $value[0] === '"' && $value[-1] === '"') {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

gu_load_env(__DIR__ . '/../.env');

function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

define('APP_ENV', env('APP_ENV', 'production'));
define('APP_NAME', env('APP_NAME', 'Gulu University'));
define('APP_BASE_URL', rtrim(env('APP_BASE_URL', '/'), '/') . '/');
define('ESERVICES_URL', env('ESERVICES_URL', 'https://eservices.gu.ac.ug'));
define('SESSION_COOKIE_NAME', env('SESSION_COOKIE_NAME', 'gu_website_session'));

// Read-only bridge into gu-services' database — see includes/gu_services_bridge.php.
define('GU_SERVICES_BASE_URL', rtrim(env('GU_SERVICES_BASE_URL', ESERVICES_URL), '/') . '/');

// APP_ENV gates error verbosity — CLAUDE.md Rule 5: never expose stack
// traces/DB errors/paths to a visitor outside development.
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}

ini_set('log_errors', '1');
$logDir = __DIR__ . '/../storage/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php-error.log');
