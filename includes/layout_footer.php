<?php
require_once __DIR__ . '/../config/config.php';
$newsletterStatus = $_GET['newsletter'] ?? null; // 'subscribed' | 'error' | null
?>
</main>

<footer class="site-footer" id="newsletter">
    <div class="container">
        <div class="footer-grid">
            <div>
                <img src="assets/logo.png" alt="Gulu University crest" style="height:64px; width:auto; margin-bottom: var(--gu-space-2);">
                <h4>Gulu University</h4>
                <p>For Community Transformation. A public university serving Northern Uganda and beyond, with campuses in Gulu, Kitgum, Kotido, Hoima, and the Moroto Constituent College.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?= esc(ESERVICES_URL) ?>" target="_blank" rel="noopener">eServices Portal</a></li>
                    <li><a href="admissions">Admissions</a></li>
                    <li><a href="academics">Academics</a></li>
                    <li><a href="careers">Careers</a></li>
                    <li><a href="downloads">Downloads &amp; Policies</a></li>
                    <li><a href="donate">Donate</a></li>
                </ul>
            </div>
            <div>
                <h4>Useful Links</h4>
                <ul>
                    <li><a href="about#governance">Governance</a></li>
                    <li><a href="news">News &amp; Press</a></li>
                    <li><a href="privacy-policy">Privacy Policy</a></li>
                    <li><a href="accessibility">Accessibility Statement</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <address style="font-style: normal;">
                    Gulu University<br>
                    P.O. Box 166, Laroo Division<br>
                    Gulu City, Uganda<br>
                    <a href="tel:+256471432095">+256-471-432095</a><br>
                    <a href="tel:+256471432648">+256-471-432648</a><br>
                    <a href="mailto:info@gu.ac.ug">info@gu.ac.ug</a>
                </address>
            </div>
        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.12); padding-top: var(--gu-space-4); margin-bottom: var(--gu-space-4);">
            <h4>Get University Updates by Email</h4>
            <?php if ($newsletterStatus === 'subscribed'): ?>
                <p style="color: var(--gu-yellow);">Thank you — you're subscribed. You can unsubscribe at any time using the link in any email we send.</p>
            <?php elseif ($newsletterStatus === 'error'): ?>
                <p style="color: var(--gu-yellow);">Please enter a valid email address and confirm your consent to subscribe.</p>
            <?php else: ?>
                <p style="margin-bottom: var(--gu-space-2);">Occasional news and announcements — no spam, unsubscribe anytime.</p>
            <?php endif; ?>
            <form method="post" action="newsletter-subscribe" novalidate>
                <?= csrf_field() ?>
                <div class="newsletter-form">
                    <label for="newsletter_email" style="position:absolute;left:-9999px;">Email address</label>
                    <input type="email" id="newsletter_email" name="email" placeholder="you@example.com" required maxlength="191">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </div>
                <label style="display:flex; align-items:flex-start; gap:0.5em; margin-top: var(--gu-space-2); font-size: 0.8rem; color: #b9c9c0;">
                    <input type="checkbox" name="consent" value="1" required style="margin-top:0.25em;">
                    I consent to receive email updates from Gulu University. Not pre-checked — see our <a href="privacy-policy">Privacy Policy</a>.
                </label>
            </form>
        </div>

        <div class="footer-bottom">
            &copy; <?= date('Y') ?> Gulu University. All rights reserved.
        </div>
    </div>
</footer>

<button type="button" class="back-to-top" aria-label="Back to top">&uarr;</button>

<script src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/../assets/js/main.js') ?>" defer></script>
</body>
</html>
