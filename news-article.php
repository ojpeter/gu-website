<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$slug = (string) ($_GET['slug'] ?? '');
$article = $slug !== '' ? gu_services_news_by_slug($slug) : null;

if (!$article) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$pageTitle = $article['meta_title'] ?: $article['title'];
$pageDescription = $article['meta_description'] ?: ($article['excerpt'] ?: $article['title']);

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container" style="max-width: 44rem;">
        <span class="eyebrow">News</span>
        <h1><?= esc($article['title']) ?></h1>
        <p class="card-meta"><?= esc(date('d F Y', strtotime($article['published_at']))) ?></p>

        <?php if (!empty($article['featured_image'])): ?>
        <img src="<?= esc(gu_services_asset_url($article['featured_image'])) ?>" alt="<?= esc($article['featured_image_alt'] ?? '') ?>" style="border-radius: var(--gu-radius); margin: var(--gu-space-4) 0;">
        <?php endif; ?>

        <div><?= nl2br(esc($article['body'])) ?></div>

        <p style="margin-top: var(--gu-space-5);"><a href="news">&larr; Back to News</a></p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
