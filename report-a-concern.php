<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Report a Concern';
$pageDescription = 'How to report a safeguarding concern or grievance to Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Safeguarding</span>
        <h1>Report a Concern</h1>
        <p class="lede">Gulu University takes safeguarding and grievance reports seriously. Because this channel can involve highly sensitive information, it is being built deliberately, with a confirmed handling process and restricted access, rather than as an ordinary contact form (requirements.md Section 14.4).</p>

        <div class="card" style="max-width: 40rem;">
            <h3>In the meantime, please contact us directly</h3>
            <p>Email: <a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a><br>
            Phone: <a href="tel:+256471432095">+256-471-432095</a></p>
            <p style="color: var(--gu-muted);">A dedicated, secure reporting channel — with a named responsible office and defined confidentiality safeguards — is in development and will replace this notice once confirmed with the university.</p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
