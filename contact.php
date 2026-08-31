<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/audit.php';

start_secure_session();

$errors = [];
$old = ['full_name' => '', 'email' => '', 'subject' => '', 'message' => ''];
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    enforce_rate_limit('contact_form', 5, 600); // 5 submissions per 10 minutes per IP

    $old = [
        'full_name' => trim((string) ($_POST['full_name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'subject' => trim((string) ($_POST['subject'] ?? '')),
        'message' => trim((string) ($_POST['message'] ?? '')),
    ];

    $v = new Validator($old);
    $v->required('full_name')->maxLength('full_name', 150);
    $v->required('email')->email('email')->maxLength('email', 191);
    $v->required('subject')->maxLength('subject', 200);
    $v->required('message')->minLength('message', 10)->maxLength('message', 5000);

    if ($v->fails()) {
        $errors = $v->errors();
    } else {
        $stmt = db()->prepare(
            'INSERT INTO contact_messages (full_name, email, subject, message, ip_address)
             VALUES (:full_name, :email, :subject, :message, :ip)'
        );
        $stmt->execute([
            'full_name' => $old['full_name'],
            'email' => $old['email'],
            'subject' => $old['subject'],
            'message' => $old['message'],
            'ip' => client_ip(),
        ]);

        audit_log('contact_message.submitted', 'contact_messages', (string) db()->lastInsertId());

        $submitted = true;
        $old = ['full_name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    }
}

$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with Gulu University — general inquiries, campus address, and phone/email contacts.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Get in Touch</span>
        <h1>Contact Us</h1>

        <div class="card-grid" style="margin-bottom: var(--gu-space-5);">
            <div class="card">
                <h3>Visit</h3>
                <p>Gulu University<br>P.O. Box 166, Laroo Division<br>Gulu City, Uganda</p>
            </div>
            <div class="card">
                <h3>Call</h3>
                <p><a href="tel:+256471432095">+256-471-432095</a><br><a href="tel:+256471432648">+256-471-432648</a></p>
            </div>
            <div class="card">
                <h3>Email</h3>
                <p><a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a></p>
            </div>
        </div>

        <div class="split-row">
            <div>
                <h2>Find Us</h2>
                <iframe
                    src="https://www.google.com/maps?q=Gulu+University,+Gulu,+Uganda&output=embed"
                    title="Map showing Gulu University's location"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen></iframe>
            </div>
            <div>
                <h2>Send Us a Message</h2>

                <?php if ($submitted): ?>
                    <div class="card" style="border-color: var(--gu-green);">
                        <p style="margin:0;">Thank you — your message has been received. We aim to respond within a few business days.</p>
                    </div>
                <?php else: ?>
                    <form method="post" action="contact" novalidate>
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
                            <label class="form-label" for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control<?= !empty($errors['subject']) ? ' has-error' : '' ?>" value="<?= esc($old['subject']) ?>" required maxlength="200">
                            <?php foreach ($errors['subject'] ?? [] as $e): ?><span class="field-error"><?= esc($e) ?></span><?php endforeach; ?>
                        </div>

                        <div class="field">
                            <label class="form-label" for="message">Message</label>
                            <textarea id="message" name="message" class="form-control<?= !empty($errors['message']) ? ' has-error' : '' ?>" required minlength="10" maxlength="5000" rows="6"><?= esc($old['message']) ?></textarea>
                            <?php foreach ($errors['message'] ?? [] as $e): ?><span class="field-error"><?= esc($e) ?></span><?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <p style="margin-top: var(--gu-space-5);">Safeguarding concern or grievance to report? See our dedicated <a href="report-a-concern">Report a Concern</a> channel.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
