<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$slug = (string) ($_GET['slug'] ?? '');
$event = $slug !== '' ? gu_services_event_by_slug($slug) : null;

if (!$event) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$pageTitle = $event['title'];
$pageDescription = $event['title'] . ' — Gulu University event' . ($event['location'] ? ' at ' . $event['location'] : '') . '.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container" style="max-width: 44rem;">
        <span class="eyebrow">Event</span>
        <h1><?= esc($event['title']) ?></h1>
        <p class="card-meta">
            <?= esc(date('l, d F Y \a\t g:ia', strtotime($event['start_datetime']))) ?>
            <?php if ($event['end_datetime']): ?> &ndash; <?= esc(date('g:ia', strtotime($event['end_datetime']))) ?><?php endif; ?>
            <?php if ($event['location']): ?><br><?= esc($event['location']) ?><?php endif; ?>
        </p>

        <?php if ($event['description']): ?>
        <div style="margin-top: var(--gu-space-4);"><?= nl2br(esc($event['description'])) ?></div>
        <?php endif; ?>

        <p style="margin-top: var(--gu-space-5);"><a href="events">&larr; Back to Events</a></p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
