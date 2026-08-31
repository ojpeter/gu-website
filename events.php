<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$pageTitle = 'Events';
$pageDescription = 'Upcoming events at Gulu University.';

$perPage = 9;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = gu_services_published_events_count(true);
$events = gu_services_published_events(true, $perPage, ($page - 1) * $perPage);
$totalPages = (int) ceil($total / $perPage);

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">What's On</span>
        <h1>Events</h1>
        <p class="lede">Upcoming events across Gulu University's campuses.</p>

        <?php if ($events): ?>
        <div class="card-grid">
            <?php foreach ($events as $event): ?>
                <div class="card">
                    <p class="card-meta"><?= esc(date('d M Y, g:ia', strtotime($event['start_datetime']))) ?><?php if ($event['location']): ?> &middot; <?= esc($event['location']) ?><?php endif; ?></p>
                    <h3><a href="event?slug=<?= urlencode($event['slug']) ?>" style="text-decoration:none; color:inherit;"><?= esc($event['title']) ?></a></h3>
                    <?php if ($event['description']): ?><p><?= esc(mb_strimwidth($event['description'], 0, 140, '…')) ?></p><?php endif; ?>
                    <a href="event?slug=<?= urlencode($event['slug']) ?>">Details</a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Event pages" style="margin-top: var(--gu-space-4); display:flex; gap: var(--gu-space-2);">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-outline' ?>" href="events?page=<?= $p ?>"><?= $p ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state">No upcoming events published at this time.</div>
        <?php endif; ?>

        <p style="margin-top: var(--gu-space-4);"><a href="news">See all news</a></p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
