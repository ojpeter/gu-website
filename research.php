<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Research';
$pageDescription = 'Research areas, publications, ethics, and partnerships at Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Research &amp; Innovation</span>
        <h1>Research</h1>
        <p class="lede">Gulu University's research agenda is centered on rural development, agriculture, health, and peace-building, coordinated by the Directorate of Research and Graduate Training.</p>

        <div class="card-grid">
            <div class="card" id="publications">
                <h3>Research Publications</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card" id="projects">
                <h3>Research Projects</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card" id="ethics">
                <h3>Research Ethics</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card" id="partnerships">
                <h3>Research Partnerships</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card" id="repository">
                <h3>Digital Repository</h3>
                <p>Browse the institutional repository: <a href="https://ir.gu.ac.ug" target="_blank" rel="noopener">ir.gu.ac.ug</a></p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
