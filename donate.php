<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/audit.php';

start_secure_session();

$errors = [];
$old = ['full_name' => '', 'email' => '', 'phone' => '', 'interest_area' => '', 'message' => ''];
$submitted = false;

$interestAreas = [
    'scholarships' => 'Student Scholarships & Bursaries',
    'infrastructure' => 'Infrastructure & Facilities',
    'research' => 'Research & Innovation',
    'community' => 'Community Transformation Projects',
    'general' => 'General / Where Most Needed',
    'other' => 'Other',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    enforce_rate_limit('donation_inquiry', 5, 600);

    $old = [
        'full_name' => trim((string) ($_POST['full_name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'interest_area' => trim((string) ($_POST['interest_area'] ?? '')),
        'message' => trim((string) ($_POST['message'] ?? '')),
    ];

    $v = new Validator($old);
    $v->required('full_name')->maxLength('full_name', 150);
    $v->required('email')->email('email')->maxLength('email', 191);
    $v->maxLength('phone', 45);
    $v->required('interest_area')->inList('interest_area', array_keys($interestAreas));
    $v->maxLength('message', 3000);

    if ($v->fails()) {
        $errors = $v->errors();
    } else {
        $stmt = db()->prepare(
            'INSERT INTO donation_inquiries (full_name, email, phone, interest_area, message, ip_address)
             VALUES (:full_name, :email, :phone, :interest_area, :message, :ip)'
        );
        $stmt->execute([
            'full_name' => $old['full_name'],
            'email' => $old['email'],
            'phone' => $old['phone'] ?: null,
            'interest_area' => $old['interest_area'],
            'message' => $old['message'] ?: null,
            'ip' => client_ip(),
        ]);

        audit_log('donation_inquiry.submitted', 'donation_inquiries', (string) db()->lastInsertId());

        $submitted = true;
        $old = ['full_name' => '', 'email' => '', 'phone' => '', 'interest_area' => '', 'message' => ''];
    }
}

$pageTitle = 'Donate';
$pageDescription = 'Support Gulu University\'s mission of community transformation — scholarships, research, infrastructure, and outreach.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="hero">
    <div class="container hero__content">
        <span class="eyebrow eyebrow--on-dark">Give / Support GU</span>
        <h1>Invest in Community Transformation</h1>
        <p class="lede">Every gift to Gulu University — scholarships, research, infrastructure, or community outreach — directly advances rural development, health, education, and peace-building across Northern Uganda.</p>
    </div>
</section>
<svg class="hero-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true" focusable="false">
    <path fill="#ffffff" d="M0,32 C240,60 480,0 720,16 C960,32 1200,56 1440,24 L1440,60 L0,60 Z"></path>
</svg>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Ways to Give</span>
        <h2>How Your Support Helps</h2>
        <div class="card-grid">
            <div class="card">
                <h3>Student Scholarships &amp; Bursaries</h3>
                <p style="color: var(--gu-muted);">Help talented students from underserved communities access higher education.</p>
            </div>
            <div class="card">
                <h3>Infrastructure &amp; Facilities</h3>
                <p style="color: var(--gu-muted);">Support classrooms, laboratories, libraries, and student accommodation.</p>
            </div>
            <div class="card">
                <h3>Research &amp; Innovation</h3>
                <p style="color: var(--gu-muted);">Fund research in agriculture, health, and peace-building that transforms communities.</p>
            </div>
            <div class="card">
                <h3>Community Transformation Projects</h3>
                <p style="color: var(--gu-muted);">Back outreach initiatives that put the university's mission into practice.</p>
            </div>
        </div>

        <div class="empty-state" style="margin-top: var(--gu-space-4);">Official bank transfer and mobile money details are being finalized with the Finance Department and will be published here. In the meantime, please use the form below and our Development team will follow up with secure payment instructions.</div>
    </div>
</section>

<section class="section section-alt reveal">
    <div class="container">
        <span class="eyebrow">Get in Touch</span>
        <h2>Tell Us About Your Gift</h2>
        <p class="lede" style="max-width: 40rem;">Share your interest below and our Development/Communications team will reach out with next steps — including major and planned gifts, and in-kind donations.</p>

        <?php if ($submitted): ?>
            <div class="card" style="border-color: var(--gu-green); max-width: 40rem;">
                <p style="margin:0;">Thank you for your generosity — we've received your inquiry and will be in touch soon.</p>
            </div>
        <?php else: ?>
            <form method="post" action="donate" novalidate style="max-width: 40rem;">
                <?= csrf_field() ?>

                <div class="field">
                    <label class="form-label" for="full_name">Full name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control<?= !empty($errors['full_name']) ? ' has-error' : '' ?>" value="<?= esc($old['full_name']) ?>" required maxlength="150">
                    <?php foreach ($errors['full_name'] ?? [] as $e): ?><span class="field-error"><?= esc($e) ?></span><?php endforeach; ?>
                </div>

                <div class="field">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-control<?= !empty($errors['email']) ? ' has-error' : '' ?>" value="<?= esc($old['email']) ?>" required maxlength="191">
                    <?php foreach ($errors['email'] ?? [] as $e): ?><span class="field-error"><?= esc($e) ?></span><?php endforeach; ?>
                </div>

                <div class="field">
                    <label class="form-label" for="phone">Phone (optional)</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= esc($old['phone']) ?>" maxlength="45">
                </div>

                <div class="field">
                    <label class="form-label" for="interest_area">I'm interested in supporting</label>
                    <select id="interest_area" name="interest_area" class="form-control<?= !empty($errors['interest_area']) ? ' has-error' : '' ?>" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($interestAreas as $value => $label): ?>
                            <option value="<?= esc($value) ?>"<?= $old['interest_area'] === $value ? ' selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php foreach ($errors['interest_area'] ?? [] as $e): ?><span class="field-error"><?= esc($e) ?></span><?php endforeach; ?>
                </div>

                <div class="field">
                    <label class="form-label" for="message">Message (optional)</label>
                    <textarea id="message" name="message" class="form-control" maxlength="3000" rows="5"><?= esc($old['message']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Inquiry</button>
            </form>
        <?php endif; ?>

        <p style="margin-top: var(--gu-space-5);">Prefer to speak with someone directly? Contact the Communications Office at <a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a> or <a href="tel:+256471432095">+256-471-432095</a>.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
