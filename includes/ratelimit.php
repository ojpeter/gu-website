<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/audit.php';

/**
 * DB-backed rate limiter — CLAUDE.md Rule 2. Every input-accepting or
 * costly route calls enforce_rate_limit() near the top, before doing any
 * real work.
 *
 * @param string $bucket   Logical action name, e.g. 'login', 'contact_form', 'search'.
 * @param int    $maxHits  Max hits allowed within the window.
 * @param int    $windowSeconds Sliding window size in seconds.
 */
function rate_limit_check(string $bucket, int $maxHits, int $windowSeconds): bool
{
    $ip = client_ip();
    $bucketKey = $bucket . ':' . $ip;

    $stmt = db()->prepare(
        'SELECT COUNT(*) AS hits FROM rate_limit_hits
         WHERE bucket_key = :bucket_key AND created_at >= (NOW() - INTERVAL :window_seconds SECOND)'
    );
    $stmt->execute(['bucket_key' => $bucketKey, 'window_seconds' => $windowSeconds]);
    $hits = (int) $stmt->fetch()['hits'];

    if ($hits >= $maxHits) {
        audit_log('throttle.violation', null, null, ['bucket' => $bucket, 'ip' => $ip, 'hits' => $hits]);
        return false;
    }

    $insert = db()->prepare('INSERT INTO rate_limit_hits (bucket_key, ip_address) VALUES (:bucket_key, :ip)');
    $insert->execute(['bucket_key' => $bucketKey, 'ip' => $ip]);

    return true;
}

/**
 * Convenience wrapper: checks the limit and, on failure, halts the request
 * with a 429 rather than letting the caller forget to check the return value.
 */
function enforce_rate_limit(string $bucket, int $maxHits, int $windowSeconds): void
{
    if (!rate_limit_check($bucket, $maxHits, $windowSeconds)) {
        http_response_code(429);
        exit('Too many requests. Please wait a moment and try again.');
    }
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
