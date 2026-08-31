<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/nav.php';

// Start the session here, once, before ANY HTML output begins — every page
// includes this partial first, so this is the one place that's guaranteed
// to run before headers are sent. Forms rendered later (e.g. the footer's
// newsletter signup) call csrf_field()/verify_csrf() themselves, but those
// only work if the session — and its cookie — already exist; starting it
// lazily inside a mid-page partial is too late, since layout_header.php has
// already flushed HTML by then (a real bug caught and fixed here: see
// progress.md).
start_secure_session();

// Every page sets these before including this partial.
$pageTitle = $pageTitle ?? APP_NAME;
$pageDescription = $pageDescription ?? 'Gulu University — For Community Transformation.';
$announcement = $announcement ?? null; // ['message' => '...', 'url' => '...'] or null

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
// Nav URLs are relative filenames (resolved via <base href> below), so
// aria-current compares against just the current page's own filename, not
// the full request path (which still includes /gu-website/ in this nested
// dev environment).
$currentPage = basename($currentPath);
// Built from the actual request, not APP_BASE_URL + path — avoids double-
// prefixing the path when this app is nested under a subfolder in dev
// (e.g. /gu-website/) rather than served from its own domain root.
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? parse_url(APP_BASE_URL, PHP_URL_HOST);
$canonicalUrl = $scheme . '://' . $host . $currentPath;
// Uses APP_BASE_URL (already correctly includes any dev subpath), not raw
// scheme+host — a real bug this fixes: the previous version silently
// dropped the /gu-website/ prefix, 404ing the OG image and JSON-LD logo
// (and, before the <base> tag/relative-href fix below existed, EVERY
// asset/link on the site 404'd for the same root-absolute-path reason —
// caught via a real Lighthouse run, not curl, which only fetches the HTML
// document itself and never notices missing linked resources).
$ogImage = APP_BASE_URL . 'assets/logo.png';
?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<!-- Sets the explicit dark/light choice (if any) BEFORE the stylesheet
     loads/paints, so a returning visitor never sees a flash of the wrong
     theme. Deliberately inline and this early, not in main.js (which loads
     deferred at the end of body, long after first paint). No CSP is set on
     this project yet (CLAUDE.md Rule 5 lists it as a standing requirement,
     not yet built) — if/when one is added, this inline script needs a
     matching nonce or it will be blocked. -->
<script>
(function () {
    try {
        var stored = localStorage.getItem('gu-theme');
        if (stored === 'dark' || stored === 'light') {
            document.documentElement.setAttribute('data-theme', stored);
        }
    } catch (e) {}
})();
</script>
<!-- Every relative href/src/action in this app (nav links, asset paths,
     form actions) is written WITHOUT a leading slash specifically so it
     resolves against this <base>, not the domain root — makes the site
     work identically whether deployed at a domain root (production) or
     nested under a subfolder (this XAMPP dev environment). Query-only
     hrefs (e.g. "?page=2") and bare-fragment hrefs (e.g. "#foo") are the
     two things that do NOT work safely under a <base> — see news.php/
     events.php's pagination (written as "news?page=2", never bare
     "?page=2") and main.js's skip-link click handler (JS-driven, not a
     bare-fragment browser navigation) for how those are handled instead. -->
<base href="<?= esc(APP_BASE_URL) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle) ?> | Gulu University</title>
<meta name="description" content="<?= esc($pageDescription) ?>">
<link rel="canonical" href="<?= esc($canonicalUrl) ?>">

<link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
<meta name="theme-color" content="#00BF63">

<!-- Open Graph / social sharing (requirements.md Section 12) -->
<meta property="og:site_name" content="Gulu University">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= esc($pageTitle) ?> | Gulu University">
<meta property="og:description" content="<?= esc($pageDescription) ?>">
<meta property="og:url" content="<?= esc($canonicalUrl) ?>">
<meta property="og:image" content="<?= esc($ogImage) ?>">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?= esc($pageTitle) ?> | Gulu University">
<meta name="twitter:description" content="<?= esc($pageDescription) ?>">

<link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollegeOrUniversity',
    'name' => 'Gulu University',
    'alternateName' => 'GU',
    'slogan' => 'For Community Transformation',
    'url' => APP_BASE_URL,
    'logo' => $ogImage,
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'P.O. Box 166, Laroo Division',
        'addressLocality' => 'Gulu City',
        'addressCountry' => 'UG',
    ],
    'telephone' => '+256-471-432095',
    'email' => 'info@gu.ac.ug',
], JSON_UNESCAPED_SLASHES) ?>
</script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<?php if ($announcement): ?>
<div class="announcement-banner" role="alert">
    <?= esc($announcement['message']) ?>
    <?php if (!empty($announcement['url'])): ?>
        <a href="<?= esc($announcement['url']) ?>">Learn more</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<header class="site-header">
    <div class="container site-header__bar">
        <a class="site-header__brand" href="index">
            <img class="site-header__brand-mark" src="assets/logo.png" alt="Gulu University crest">
            <span>Gulu University</span>
        </a>

        <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="Toggle menu">
            <span></span>
        </button>

        <nav class="primary-nav" id="primary-nav" aria-label="Primary">
            <?php foreach (primary_nav_items() as $item): ?>
                <?php if (!empty($item['groups'])): ?>
                <div class="nav-item-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="<?= esc($item['url']) ?>" aria-haspopup="true" aria-expanded="false"<?= $currentPage === $item['url'] ? ' aria-current="page"' : '' ?>>
                        <?= nav_icon($item['icon'] ?? '') ?><?= esc($item['label']) ?> <span class="nav-caret" aria-hidden="true"></span>
                    </a>
                    <div class="nav-dropdown-panel">
                        <?php foreach ($item['groups'] as $groupLabel => $links): ?>
                        <div class="nav-dropdown-col">
                            <h4 class="nav-dropdown-heading"><?= esc($groupLabel) ?></h4>
                            <ul>
                                <?php foreach ($links as $link): ?>
                                <li><a href="<?= esc($link['url']) ?>"<?= !empty($link['external']) ? ' target="_blank" rel="noopener"' : '' ?>><?= esc($link['label']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <a class="nav-link" href="<?= esc($item['url']) ?>"<?= $currentPage === $item['url'] ? ' aria-current="page"' : '' ?>>
                    <?= nav_icon($item['icon'] ?? '') ?><?= esc($item['label']) ?>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="header-ctas">
            <button type="button" class="theme-toggle" id="theme-toggle" aria-label="Switch to dark theme" aria-pressed="false">
                <svg class="theme-toggle__icon theme-toggle__icon--sun" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.4M12 19.1v2.4M4.9 4.9l1.7 1.7M17.4 17.4l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.9 19.1l1.7-1.7M17.4 6.6l1.7-1.7"/></svg>
                <svg class="theme-toggle__icon theme-toggle__icon--moon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a6.8 6.8 0 0 0 10.5 10.5Z"/></svg>
            </button>
            <a class="btn btn-sm btn-outline" href="<?= esc(ESERVICES_URL) ?>" target="_blank" rel="noopener">
                eServices
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg>
                <span style="position:absolute;left:-9999px;">(opens in a new tab)</span>
            </a>
            <a class="btn btn-sm btn-primary" href="admissions">Apply Now</a>
        </div>
    </div>
</header>

<main id="main-content" tabindex="-1">
