<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$pageTitle = 'Home';
$pageDescription = 'Gulu University — For Community Transformation. Explore academics, admissions, research, and student life at Gulu University, Northern Uganda.';

// requirements.md Section 10.3: never show a placeholder "0" — only render
// facts that University PRO has approved and published in GU-CMS (gu-services),
// each with a real as-of date. If none are published yet (or the bridge is
// unreachable), the section is simply omitted — never a placeholder zero.
$facts = gu_services_published_facts();
$latestNews = gu_services_published_news(3);
$upcomingEvents = gu_services_published_events(true, 3);
// For the "Search for a Programme" box's Faculty/Institute and Programme
// Type selects — real, live data (see academics.php, which actually runs
// the search these fields submit to).
$searchFacultiesAndInstitutes = array_filter(
    gu_services_academic_units(),
    fn($u) => in_array($u['type'], ['faculty', 'institute', 'directorate'], true)
);
$searchLevelOptions = gu_services_lookup_values('level_of_study');

require __DIR__ . '/includes/layout_header.php';
?>

<?php
// Alternating text/illustration showcase slides. Slide 0 carries the site's
// one <h1> and the former standalone hero's message/CTAs — folded in here
// (rather than kept as a separate section above) specifically so the top
// nav and the slider are both visible in the first viewport, without
// scrolling (client instruction). Its picture side is the real university
// crest (assets/logo.png) rather than an icon, since that's genuine
// institutional imagery, not a stand-in. Slides 1-3 are real institutional
// themes (Academics, Research, Student Life) each pointing at their real
// page. No campus photography has been supplied yet (progress.md), so
// their "picture" side is an honest brand illustration (icon + gradient
// blobs) rather than a stock photo standing in for one.
$homeSlides = [
    [
        'eyebrow' => 'Gulu University',
        'title' => 'For Community Transformation',
        'text' => 'A public university in Northern Uganda, advancing agriculture, health, education, business, law, and peace-building research and training across six faculties and four campuses.',
        'ctas' => [
            ['label' => 'Apply Now', 'url' => 'admissions', 'variant' => 'btn-primary'],
            ['label' => 'Explore Programmes', 'url' => 'academics', 'variant' => 'btn-outline'],
        ],
        'image' => 'assets/logo.png',
        'image_alt' => 'Gulu University crest',
        'accent' => 'slide-visual--brand',
    ],
    [
        'eyebrow' => 'Academics',
        'title' => 'Six Faculties. One Mission.',
        'text' => 'From Agriculture and Environment to Medicine, Law, Science, Business, and Education — Gulu University trains the graduates Northern Uganda needs, across undergraduate and postgraduate programmes.',
        'ctas' => [['label' => 'Explore Programmes', 'url' => 'academics', 'variant' => 'btn-primary']],
        'icon' => 'study',
        'accent' => '',
    ],
    [
        'eyebrow' => 'Research',
        'title' => 'Research That Transforms Communities',
        'text' => 'Our researchers work across agriculture, health, and peace-building — turning evidence into policy and practice for Northern Uganda and beyond.',
        'ctas' => [['label' => 'See Research', 'url' => 'research', 'variant' => 'btn-primary']],
        'icon' => 'research',
        'accent' => '',
    ],
    [
        'eyebrow' => 'Student Life',
        'title' => 'A Vibrant Life on Campus',
        'text' => 'Guild leadership, sports, faith communities, housing, health, and counselling — student life at Gulu University is built to support every student well beyond the classroom.',
        'ctas' => [['label' => 'Discover Student Life', 'url' => 'student-life', 'variant' => 'btn-primary']],
        'icon' => 'community',
        'accent' => '',
    ],
];
?>
<section class="section reveal home-slider-section">
    <div class="container">
        <div class="home-slider" id="home-slider">
            <div class="home-slider__viewport">
                <div class="home-slider__track">
                    <?php foreach ($homeSlides as $i => $slide): ?>
                    <article class="slide<?= $i % 2 === 1 ? ' is-reverse' : '' ?>" aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>" aria-roledescription="slide" aria-label="<?= $i + 1 ?> of <?= count($homeSlides) ?>">
                        <div class="slide__media">
                            <div class="slide-visual <?= esc($slide['accent']) ?>">
                                <?php if (!empty($slide['image'])): ?>
                                    <img class="slide-visual__image" src="<?= esc($slide['image']) ?>" alt="<?= esc($slide['image_alt']) ?>">
                                <?php else: ?>
                                    <div class="slide-visual__icon"><?= nav_icon($slide['icon'], 72) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="slide__text">
                            <span class="eyebrow"><?= esc($slide['eyebrow']) ?></span>
                            <?php if ($i === 0): ?>
                                <h1><?= esc($slide['title']) ?></h1>
                            <?php else: ?>
                                <h2><?= esc($slide['title']) ?></h2>
                            <?php endif; ?>
                            <p class="lede"><?= esc($slide['text']) ?></p>
                            <p class="slide__ctas">
                                <?php foreach ($slide['ctas'] as $cta): ?>
                                    <a class="btn <?= esc($cta['variant']) ?>" href="<?= esc($cta['url']) ?>"><?= esc($cta['label']) ?></a>
                                <?php endforeach; ?>
                            </p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="home-slider__controls">
                <button type="button" class="slider-btn slider-prev" aria-label="Previous slide">&#8249;</button>
                <div class="slider-dots" role="tablist" aria-label="Choose slide"></div>
                <button type="button" class="slider-btn slider-next" aria-label="Next slide">&#8250;</button>
            </div>
            <div class="slider-progress" aria-hidden="true"><div class="slider-progress__bar"></div></div>
        </div>
    </div>
</section>

<section class="section reveal" style="padding-top: 0; padding-bottom: 0;">
    <div class="container">
        <?php
        // Founding years are real, verifiable institutional facts, not
        // decoration — only Main Campus's is confirmed, so only it gets a
        // "Since" line. Fabricating years for the other four would violate
        // the same anti-placeholder principle applied to every other stat
        // on this site (never show unverified institutional data as fact).
        $campuses = [
            ['name' => 'Main Campus', 'since' => '2003'],
            ['name' => 'Kitgum Campus', 'since' => null],
            ['name' => 'Hoima Campus', 'since' => null],
            ['name' => 'Moroto Campus', 'since' => null],
            ['name' => 'Kotido Campus', 'since' => null],
        ];
        ?>
        <div class="campus-row">
            <h2 class="campus-strip-heading">Our Campuses</h2>
            <div class="campus-strip" aria-label="Our campuses">
                <?php foreach ($campuses as $campus): ?>
                <a class="campus-card" href="about#our-campuses">
                    <?= nav_icon('pin', 18) ?>
                    <span class="campus-card__text">
                        <span class="campus-card__name"><?= esc($campus['name']) ?></span>
                        <?php if ($campus['since']): ?><span class="campus-card__since">Since <?= esc($campus['since']) ?></span><?php endif; ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section reveal">
    <div class="container">
        <div class="split-row">
            <div class="vc-message">
                <div class="vc-message__avatar-ring">
                    <img class="vc-message__avatar" src="assets/vc.jpg" alt="Prof. Openjuru L. George, Vice Chancellor of Gulu University">
                </div>
                <div class="vc-message__body">
                    <span class="eyebrow">A Message from the Vice Chancellor</span>
                    <blockquote>
                        <p>&ldquo;Anybody interested in rural development or community transformation should come to Gulu University. This call is based on the fact that Gulu University is an agriculturally focused university, and all our programs are geared towards the promotion of agri-activity for the rural community.&rdquo;</p>
                    </blockquote>
                    <p class="vc-message__name">Prof. Openjuru L. George</p>
                    <p class="vc-message__title">Vice Chancellor, Gulu University</p>
                    <a class="btn btn-primary vc-message__cta" href="about#vice-chancellor">Read More &#8594;</a>
                </div>
            </div>

            <div class="programme-search">
                <span class="eyebrow">Find Your Fit</span>
                <h2>Search for a Programme</h2>
                <p style="color: var(--gu-muted);">Search by Keyword, Programme Type, and/or Faculty or Institute to find where your area of interest is offered.</p>
                <form action="academics" method="get" class="programme-search__form">
                    <div class="field" style="margin-bottom: var(--gu-space-2);">
                        <label class="form-label" for="programme-search-q">Keyword</label>
                        <input type="search" id="programme-search-q" name="q" class="form-control" placeholder="e.g. Computer Science, History&hellip;" maxlength="100">
                    </div>
                    <div class="field" style="margin-bottom: var(--gu-space-2);">
                        <label class="form-label" for="programme-search-level">Programme Type</label>
                        <select id="programme-search-level" name="level" class="form-control">
                            <option value="">All Types</option>
                            <?php foreach ($searchLevelOptions as $lvl): ?>
                                <option value="<?= esc($lvl['value']) ?>"><?= esc($lvl['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field" style="margin-bottom: var(--gu-space-2);">
                        <label class="form-label" for="programme-search-unit">Faculty / Institute</label>
                        <select id="programme-search-unit" name="unit" class="form-control">
                            <option value="">All Faculties &amp; Institutes</option>
                            <?php foreach ($searchFacultiesAndInstitutes as $unit): ?>
                                <option value="<?= esc((string) $unit['id']) ?>"><?= esc($unit['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= nav_icon('study', 16) ?> Search</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section reveal" style="padding-top: 0;">
    <div class="container">
        <div class="eservices-callout">
            <div>
                <span class="eyebrow">One place for everything</span>
                <h2 style="margin-bottom: var(--gu-space-1);">eServices Portal</h2>
                <p style="margin: 0;">Students, staff, and applicants: access the Student Portal, Staff Portal, Admissions, Fees, E-Learning, and more from one place.</p>
            </div>
            <a class="btn btn-primary" href="<?= esc(ESERVICES_URL) ?>" target="_blank" rel="noopener">Go to eServices</a>
        </div>
    </div>
</section>

<?php if ($facts): ?>
<section class="section reveal">
    <div class="container">
        <div class="facts-strip">
            <?php foreach ($facts as $fact): ?>
                <div class="fact">
                    <span class="fact-value"><?= esc($fact['value']) ?></span>
                    <span class="fact-label"><?= esc($fact['label']) ?></span>
                    <span class="fact-as-of">As of <?= esc(date('F Y', strtotime($fact['as_of_date']))) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section reveal">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Stay informed</span>
                <h2>Latest News</h2>
            </div>
            <a href="news">View all news</a>
        </div>
        <?php if ($latestNews): ?>
        <div class="card-grid">
            <?php foreach ($latestNews as $item): ?>
                <div class="card">
                    <div class="card-icon"><?= nav_icon('news', 20) ?></div>
                    <p class="card-meta"><?= esc(date('d M Y', strtotime($item['published_at']))) ?></p>
                    <h3><a href="news-article?slug=<?= urlencode($item['slug']) ?>"><?= esc($item['title']) ?></a></h3>
                    <?php if ($item['excerpt']): ?><p><?= esc($item['excerpt']) ?></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">No news at this time. Please check back soon.</div>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt reveal">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">What's on</span>
                <h2>Upcoming Events</h2>
            </div>
            <a href="events">View all events</a>
        </div>
        <?php if ($upcomingEvents): ?>
        <div class="card-grid">
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="card">
                    <div class="card-icon"><?= nav_icon('calendar', 20) ?></div>
                    <p class="card-meta"><?= esc(date('d M Y, g:ia', strtotime($event['start_datetime']))) ?><?php if ($event['location']): ?> &middot; <?= esc($event['location']) ?><?php endif; ?></p>
                    <h3><a href="event?slug=<?= urlencode($event['slug']) ?>"><?= esc($event['title']) ?></a></h3>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">No upcoming events published at this time.</div>
        <?php endif; ?>
    </div>
</section>

<section class="section reveal">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Academics</span>
                <h2>Our Faculties</h2>
            </div>
            <a href="academics">View all academics</a>
        </div>
        <div class="card-grid">
            <?php
            $faculties = [
                ['name' => 'Agriculture and Environment', 'slug' => 'agriculture-and-environment', 'text' => 'Training agronomists, environmental scientists, and food-security specialists for Northern Uganda\'s largely agrarian economy.'],
                ['name' => 'Business and Development Studies', 'slug' => 'business-and-development-studies', 'text' => 'Building entrepreneurs, managers, and development practitioners for East Africa\'s growing economy.'],
                ['name' => 'Education and Humanities', 'slug' => 'education-and-humanities', 'text' => 'Preparing teachers, linguists, and social scientists shaping the region\'s schools and communities.'],
                ['name' => 'Law', 'slug' => 'law', 'text' => 'Educating the region\'s next generation of advocates, judges, and legal scholars.'],
                ['name' => 'Medicine', 'slug' => 'medicine', 'text' => 'Training doctors and health professionals to serve Northern Uganda\'s growing healthcare needs.'],
                ['name' => 'Science', 'slug' => 'science', 'text' => 'Advancing research and training across the physical, biological, and computing sciences.'],
            ];
            foreach ($faculties as $faculty): ?>
                <div class="card">
                    <div class="card-icon"><?= nav_icon('study', 20) ?></div>
                    <p class="card-meta">Faculty</p>
                    <h3><a href="academics#<?= esc($faculty['slug']) ?>">Faculty of <?= esc($faculty['name']) ?></a></h3>
                    <p><?= esc($faculty['text']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section cta-banner reveal">
    <div class="container cta-banner__content">
        <span class="eyebrow eyebrow--on-dark">Ready When You Are</span>
        <h2>Begin Your Journey at Gulu University</h2>
        <p class="lede">Applications for undergraduate and postgraduate programmes are open. Join a community shaping Northern Uganda's future.</p>
        <div class="cta-banner__actions">
            <a class="btn btn-primary" href="admissions">Apply Now</a>
            <a class="btn btn-outline" href="contact">Contact Admissions</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
