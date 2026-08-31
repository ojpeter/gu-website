<?php
require_once __DIR__ . '/includes/security.php';

http_response_code(404);

$pageTitle = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found on the Gulu University website.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container" style="max-width: 36rem; text-align: center;">
        <span class="eyebrow" style="justify-content:center;">Error 404</span>
        <h1>Page Not Found</h1>
        <p class="lede">Sorry, we couldn't find the page you were looking for. It may have been moved or the link may be out of date.</p>
        <p>
            <a class="btn btn-primary" href="index">Back to Home</a>
            <a class="btn btn-outline" href="contact">Contact Us</a>
        </p>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
