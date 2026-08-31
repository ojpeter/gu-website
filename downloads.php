<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Downloads & Policies';
$pageDescription = 'Downloadable forms, policies, and official documents from Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Resources</span>
        <h1>Downloads &amp; Policies</h1>
        <p class="lede">Public policy documents and forms will be published here — eventually sourced from the university's document management system so there is one canonical copy of every policy (requirements.md Section 3.3, Phase 2+).</p>
        <div class="empty-state">No documents have been published yet.</div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
