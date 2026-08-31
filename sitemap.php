<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

header('Content-Type: application/xml; charset=UTF-8');

$staticPages = [
    '/index.php', '/about.php', '/academics.php', '/admissions.php', '/research.php',
    '/student-life.php', '/community-engagement.php', '/services.php', '/staff-directory.php',
    '/news.php', '/events.php', '/careers.php', '/downloads.php', '/contact.php',
    '/privacy-policy.php', '/accessibility.php',
];

// All published news/events, not just upcoming — a sitemap should list
// everything indexable, including past events, for search completeness.
$news = gu_services_published_news();
$events = gu_services_published_events(false);

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $path): ?>
    <url>
        <loc><?= htmlspecialchars(rtrim(APP_BASE_URL, '/') . $path, ENT_XML1) ?></loc>
    </url>
<?php endforeach; ?>
<?php foreach ($news as $item): ?>
    <url>
        <loc><?= htmlspecialchars(rtrim(APP_BASE_URL, '/') . '/news-article?slug=' . urlencode($item['slug']), ENT_XML1) ?></loc>
        <lastmod><?= date('c', strtotime($item['updated_at'])) ?></lastmod>
    </url>
<?php endforeach; ?>
<?php foreach ($events as $item): ?>
    <url>
        <loc><?= htmlspecialchars(rtrim(APP_BASE_URL, '/') . '/event?slug=' . urlencode($item['slug']), ENT_XML1) ?></loc>
        <lastmod><?= date('c', strtotime($item['updated_at'])) ?></lastmod>
    </url>
<?php endforeach; ?>
</urlset>
