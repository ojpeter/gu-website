<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$pageTitle = 'Staff Directory';
$pageDescription = 'Find Gulu University staff by name, department, or specialization.';

$profiles = gu_services_published_staff_profiles();

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Governance</span>
        <h1>Staff Directory</h1>
        <p class="lede">Staff members submit and maintain their own public profile — including title, specialization, publications, and student supervisions — which is reviewed and approved by University PRO before appearing here.</p>

        <?php if ($profiles): ?>
        <div class="card-grid">
            <?php foreach ($profiles as $profile): ?>
                <?php
                $publications = gu_services_staff_publications((int) $profile['id']);
                $supervisions = gu_services_staff_supervisions((int) $profile['id']);
                ?>
                <div class="card">
                    <?php if (!empty($profile['photo'])): ?>
                    <img src="<?= esc(gu_services_asset_url($profile['photo'])) ?>" alt="<?= esc($profile['photo_alt'] ?? '') ?>" style="width:72px; height:72px; border-radius:50%; object-fit:cover; margin-bottom: var(--gu-space-2);">
                    <?php endif; ?>
                    <h3><?= esc($profile['display_name']) ?></h3>
                    <p class="card-meta"><?= esc($profile['title_position'] ?? '') ?><?php if ($profile['department_office']): ?> &middot; <?= esc($profile['department_office']) ?><?php endif; ?></p>
                    <?php if ($profile['specialization']): ?><p><strong>Specialization:</strong> <?= esc($profile['specialization']) ?></p><?php endif; ?>
                    <?php if ($profile['bio']): ?><p><?= esc(mb_strimwidth($profile['bio'], 0, 160, '…')) ?></p><?php endif; ?>
                    <?php if ($publications): ?>
                    <p class="card-meta" style="margin-top: var(--gu-space-2);">Publications (<?= count($publications) ?>)</p>
                    <ul style="margin:0; padding-left: 1.1em; font-size: 0.85rem;">
                        <?php foreach (array_slice($publications, 0, 3) as $pub): ?>
                        <li><?= esc($pub['title']) ?><?= $pub['publication_year'] ? ' (' . (int) $pub['publication_year'] . ')' : '' ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <?php if ($supervisions): ?>
                    <p class="card-meta" style="margin-top: var(--gu-space-2);">Student Supervisions (<?= count($supervisions) ?>)</p>
                    <?php endif; ?>
                    <?php if ($profile['public_email']): ?><p style="margin-top: var(--gu-space-2);"><a href="mailto:<?= esc($profile['public_email']) ?>"><?= esc($profile['public_email']) ?></a></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">No approved staff profiles are published yet.</div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
