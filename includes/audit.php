<?php

require_once __DIR__ . '/../config/db.php';

/**
 * Append-only, hash-chained audit log — CLAUDE.md Rule 5. Every admin
 * publish/edit/delete/permission-change and every throttle violation
 * writes here. Each row's hash covers its own data plus the previous
 * row's hash, so a tampered/deleted row breaks the chain detectably.
 */
function audit_log(string $action, ?string $entityType = null, ?string $entityId = null, array $details = []): void
{
    $pdo = db();

    $actorType = 'anonymous';
    $actorId = null;
    if (!empty($_SESSION['admin_user_id'])) {
        $actorType = 'admin';
        $actorId = (int) $_SESSION['admin_user_id'];
    } elseif (php_sapi_name() === 'cli') {
        $actorType = 'system';
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_SLASHES) : null;

    $prevHash = $pdo->query('SELECT hash FROM audit_log ORDER BY id DESC LIMIT 1')->fetchColumn();
    $prevHash = $prevHash ?: str_repeat('0', 64);

    $payload = implode('|', [$actorType, (string) $actorId, $action, (string) $entityType, (string) $entityId, (string) $ip, (string) $detailsJson, $prevHash]);
    $hash = hash('sha256', $payload);

    $stmt = $pdo->prepare(
        'INSERT INTO audit_log (actor_type, actor_id, action, entity_type, entity_id, ip_address, details, prev_hash, hash)
         VALUES (:actor_type, :actor_id, :action, :entity_type, :entity_id, :ip, :details, :prev_hash, :hash)'
    );
    $stmt->execute([
        'actor_type' => $actorType,
        'actor_id' => $actorId,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'ip' => $ip,
        'details' => $detailsJson,
        'prev_hash' => $prevHash,
        'hash' => $hash,
    ]);
}
