<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/audit.php';

start_secure_session();

function newsletter_redirect(string $status): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/index.php';
    $host = parse_url($referer, PHP_URL_HOST);
    $path = ($host === null || $host === ($_SERVER['HTTP_HOST'] ?? null))
        ? (parse_url($referer, PHP_URL_PATH) ?: '/index.php')
        : '/index.php';

    header('Location: ' . $path . '?newsletter=' . urlencode($status) . '#newsletter');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

verify_csrf();
enforce_rate_limit('newsletter_signup', 5, 600);

$email = trim((string) ($_POST['email'] ?? ''));
$consent = isset($_POST['consent']) && $_POST['consent'] === '1';

$v = new Validator(['email' => $email]);
$v->required('email')->email('email')->maxLength('email', 191);

if ($v->fails() || !$consent) {
    newsletter_redirect('error');
}

$token = bin2hex(random_bytes(32));

$stmt = db()->prepare(
    'INSERT INTO newsletter_subscribers (email, unsubscribe_token, ip_address)
     VALUES (:email, :token, :ip)
     ON DUPLICATE KEY UPDATE status = "subscribed", unsubscribed_at = NULL'
);
$stmt->execute([
    'email' => $email,
    'token' => $token,
    'ip' => client_ip(),
]);

audit_log('newsletter.subscribed', 'newsletter_subscribers', $email);

newsletter_redirect('subscribed');
