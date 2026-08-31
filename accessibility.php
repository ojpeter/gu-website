<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Accessibility Statement';
$pageDescription = 'Gulu University\'s commitment to a website accessible to all visitors, and how to report an accessibility barrier.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container" style="max-width: 48rem;">
        <span class="eyebrow">Commitment</span>
        <h1>Accessibility Statement</h1>
        <p>Gulu University is committed to making this website usable by everyone, including people with disabilities — directly in line with the university's own mission of community transformation and inclusion.</p>

        <h2>Conformance target</h2>
        <p>This website is being built to conform to the <strong>Web Content Accessibility Guidelines (WCAG) 2.2, Level AA</strong> (requirements.md Section 15). This is an in-progress build — not every page has completed a full manual accessibility review yet.</p>

        <h2>Known limitations</h2>
        <p>As the site is still being developed, some pages currently show placeholder "content pending" notices rather than full content. These will be replaced with fully reviewed, accessible content as each section is built.</p>

        <h2>Reporting a barrier</h2>
        <p>If you encounter any accessibility barrier on this site, please let us know: <a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a>. Please describe the page and the issue you experienced — we will work to address it.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
