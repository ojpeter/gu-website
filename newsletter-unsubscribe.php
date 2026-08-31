<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/audit.php';

$token = (string) ($_GET['token'] ?? '');
$done = false;

if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token)) {
    $stmt = db()->prepare(
        "UPDATE newsletter_subscribers
         SET status = 'unsubscribed', unsubscribed_at = NOW()
         WHERE unsubscribe_token = :token AND status = 'subscribed'"
    );
    $stmt->execute(['token' => $token]);
    if ($stmt->rowCount() > 0) {
        audit_log('newsletter.unsubscribed', 'newsletter_subscribers', $token);
        $done = true;
    }
}

$pageTitle = 'Unsubscribe';
$pageDescription = 'Unsubscribe from the Gulu University newsletter.';

require __DIR__ . '/includes/layout_header.php';
?>
<section class="section reveal">
    <div class="container" style="max-width: 32rem;">
        <span class="eyebrow">Newsletter</span>
        <h1>Unsubscribe</h1>
        <?php if ($done): ?>
            <p>You have been unsubscribed. You will no longer receive newsletter emails from Gulu University.</p>
        <?php else: ?>
            <p>This unsubscribe link is invalid or has already been used. If you keep receiving emails, contact <a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a>.</p>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
