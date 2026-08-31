<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Student Life';
$pageDescription = 'Campus life, the student guild, sports, accommodation, and wellbeing at Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Campus Life</span>
        <h1>Student Life</h1>
        <p class="lede">Life at Gulu University extends well beyond the classroom — student governance, sports, accommodation, and support services.</p>

        <div class="card-grid">
            <div class="card" id="guild">
                <h3>Guild / Student Government</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card">
                <h3>E-Learning</h3>
                <p>Access course materials at <a href="https://guele.gu.ac.ug" target="_blank" rel="noopener">guele.gu.ac.ug</a></p>
            </div>
            <div class="card" id="sports">
                <h3>Games and Sports</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card" id="accommodation">
                <h3>Accommodation &amp; Student Housing</h3>
                <p style="color: var(--gu-muted);">Content pending publication.</p>
            </div>
            <div class="card" id="health">
                <h3>Health &amp; Medical Access</h3>
                <p style="color: var(--gu-muted);">Content pending publication. Have a concern to report? See <a href="report-a-concern">Report a Concern</a>.</p>
            </div>
            <div class="card" id="notice-board">
                <h3>Notice Board</h3>
                <div class="empty-state">No notices at this time.</div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
