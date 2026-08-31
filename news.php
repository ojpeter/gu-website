<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$pageTitle = 'News';
$pageDescription = 'Latest news and press releases from Gulu University.';

$perPage = 9;
$page = max(1, (int) ($_GET['page'] ?? 1));
$total = gu_services_published_news_count();
$news = gu_services_published_news($perPage, ($page - 1) * $perPage);
$totalPages = (int) ceil($total / $perPage);

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Stay Informed</span>
        <h1>News</h1>
        <p class="lede">The latest announcements and stories from across Gulu University.</p>

        <?php if ($news): ?>
        <div class="card-grid">
            <?php foreach ($news as $item): ?>
                <div class="card">
                    <p class="card-meta"><?= esc(date('d M Y', strtotime($item['published_at']))) ?></p>
                    <h3><a href="news-article?slug=<?= urlencode($item['slug']) ?>" style="text-decoration:none; color:inherit;"><?= esc($item['title']) ?></a></h3>
                    <?php if ($item['excerpt']): ?><p><?= esc($item['excerpt']) ?></p><?php endif; ?>
                    <a href="news-article?slug=<?= urlencode($item['slug']) ?>">Read more</a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <nav aria-label="News pages" style="margin-top: var(--gu-space-4); display:flex; gap: var(--gu-space-2);">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a class="btn btn-sm <?= $p === $page ? 'btn-primary' : 'btn-outline' ?>" href="news?page=<?= $p ?>"><?= $p ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state">No news at this time. Please check back soon.</div>
        <?php endif; ?>

        <h2 style="margin-top: var(--gu-space-5);">Press &amp; Media</h2>
        <p>Media inquiries: <a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a></p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
