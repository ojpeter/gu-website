<?php

require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/nav.php';

// Only a known, registered slug renders — never arbitrary user input, so
// this can't become an open content-injection or SSRF-style vector.
$registry = generic_page_registry();
$slug = (string) ($_GET['slug'] ?? '');

if (!isset($registry[$slug])) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$entry = $registry[$slug];
$pageTitle = $entry['title'];
$pageDescription = $entry['title'] . ' — Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container" style="max-width: 40rem;">
        <span class="eyebrow"><?= esc($entry['eyebrow']) ?></span>
        <h1><?= esc($entry['title']) ?></h1>
        <div class="empty-state">This page is being finalized by the Communications Office and will be published here soon.</div>
        <p style="margin-top: var(--gu-space-4);"><a href="contact">Contact us</a> if you need this information sooner.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
