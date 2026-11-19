<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'About';
$pageDescription = 'About Gulu University - mission, governance, campuses, and facts.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">About Us</span>
        <h1>About Gulu University</h1>
        <p class="lede">Gulu University is a public university in Northern Uganda - <strong>"For Community Transformation."</strong> The university advances rural development, agriculture, health, education, business, law, and peace-building through teaching, research, and community engagement.</p>
    </div>
</section>

<section class="section section-alt reveal" id="vision-mission">
    <div class="container">
        <h2>Vision and Mission</h2>
        <div class="card-grid">
            <div class="card">
                <h3>Mission</h3>
                <p>"For Community Transformation" - advancing rural development, agriculture, health, education, business, law, and peace-building through teaching, research, and community engagement.</p>
            </div>
            <div class="card">
                <h3>Vision</h3>
                <p style="color: var(--gu-muted);">Full vision statement text is being confirmed with the Communications Office and will be published here.</p>
            </div>
        </div>
    </div>
</section>

<section class="section reveal" id="governance">
    <div class="container">
        <h2>Governance</h2>
        <div class="card-grid">
            <div class="card" id="chancellor">
                <h3>The Chancellor</h3>
                <p class="card-meta">Chancellor</p>
                <p style="color: var(--gu-muted);">Profile being confirmed with the Communications Office.</p>
            </div>
            <div class="card" id="vice-chancellor">
                <img src="assets/vc.jpg" alt="Prof. Openjuru L. George, Vice Chancellor of Gulu University" style="width:72px; height:72px; border-radius:50%; object-fit:cover; margin-bottom: var(--gu-space-2);">
                <h3>The Vice Chancellor</h3>
                <p class="card-meta">Prof. Openjuru L. George</p>
                <blockquote>
                    <p>&ldquo;Anybody interested in rural development or community transformation should come to Gulu University. This call is based on the fact that Gulu University is an agriculturally focused university, and all our programs are geared towards the promotion of agri-activity for the rural community. This focus includes Medicine, where we train for rural-based health facilities; in Education, we train teachers for rural schools and Agricultural Education.&rdquo;</p>
                </blockquote>
            </div>
            <div class="card" id="council">
                <h3>University Council</h3>
                <p class="card-meta">Governance body</p>
            </div>
            <div class="card" id="management">
                <h3>University Management</h3>
                <p class="card-meta">Management team</p>
            </div>
            <div class="card" id="senate">
                <h3>University Senate</h3>
                <p class="card-meta">Academic governance body</p>
            </div>
        </div>
        <p style="margin-top: var(--gu-space-3); color: var(--gu-muted);">Full governance profiles (Chancellor, Council membership, Management team) are being verified with the Communications Office and will be published here. See also the <a href="staff-directory">Staff Directory</a>.</p>
    </div>
</section>

<section class="section reveal" id="our-campuses">
    <div class="container">
        <h2>Our Campuses</h2>
        <div class="card-grid">
            <div class="card"><h3>Main Campus</h3><p class="card-meta">Gulu City</p></div>
            <div class="card"><h3>Kitgum Campus</h3></div>
            <div class="card"><h3>Kotido Campus</h3></div>
            <div class="card"><h3>Hoima Campus</h3></div>
            <div class="card"><h3>Moroto Constituent College</h3></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
