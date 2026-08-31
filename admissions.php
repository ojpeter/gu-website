<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Admissions';
$pageDescription = 'How to apply to Gulu University — undergraduate, postgraduate, and international admissions.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Join Us</span>
        <h1>Admissions</h1>
        <p class="lede">Ready to join Gulu University? Applications for undergraduate and postgraduate programmes are submitted online.</p>

        <div class="eservices-callout" style="margin-bottom: var(--gu-space-5);">
            <div>
                <h2 style="margin-bottom: var(--gu-space-1);">Apply Online</h2>
                <p style="margin:0;">Submit your application through the university's online admissions system.</p>
            </div>
            <a class="btn btn-primary" href="https://apply.gu.ac.ug" target="_blank" rel="noopener">Apply Now</a>
        </div>

        <div class="card-grid">
            <div class="card" id="undergraduate">
                <h3>Undergraduate Programmes</h3>
                <p style="color: var(--gu-muted);">Entry requirements and programme details are being finalized for publication here.</p>
            </div>
            <div class="card" id="postgraduate">
                <h3>Postgraduate Programmes</h3>
                <p style="color: var(--gu-muted);">Entry requirements and programme details are being finalized for publication here.</p>
            </div>
            <div class="card" id="international">
                <h3>International Students</h3>
                <p style="color: var(--gu-muted);">Guidance for international applicants is being finalized for publication here.</p>
            </div>
            <div class="card" id="fees-scholarships">
                <h3>Fees &amp; Scholarships</h3>
                <p style="color: var(--gu-muted);">A current fee schedule and scholarship/bursary information will be published here.</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
