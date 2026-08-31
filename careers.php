<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Careers';
$pageDescription = 'Vacancies and career opportunities at Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Work With Us</span>
        <h1>Careers at Gulu University</h1>
        <p class="lede">Publicly-advertised vacancies will be listed here, read-only, sourced from the university's HR recruitment system once the eServices integration (requirements.md Section 3.3, Phase 2+) is in place. Applications are always processed through the proper HR channel, not this website.</p>
        <div class="empty-state">No vacancies are currently advertised.</div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
