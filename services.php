<?php
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Services';
$pageDescription = 'University services, administrative offices, and key units at Gulu University.';

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Administrative Offices</span>
        <h1>Services</h1>
        <p class="lede">The offices and units that keep Gulu University running — from the Library and Computing Services to Finance and the University Hospital.</p>

        <h2>University Services</h2>
        <div class="card-grid">
            <?php foreach ([
                'communications-office' => 'Communications Office',
                'university-library' => 'University Library',
                'research-laboratories' => 'Multifunctional Research Laboratories',
                'computing-services' => 'Computing Services',
                'finance-department' => 'Finance Department',
                'university-hospital' => 'University Hospital',
                'quality-assurance' => 'Quality Assurance',
                'estates-and-works' => 'Estates and Works',
            ] as $slug => $label): ?>
                <div class="card" id="<?= esc($slug) ?>">
                    <h3><?= esc($label) ?></h3>
                    <p style="color: var(--gu-muted);">Contact details and services are being finalized for publication here.</p>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top: var(--gu-space-5);">Key Units &amp; Offices</h2>
        <div class="card-grid">
            <?php foreach ([
                'university-secretary' => 'University Secretary',
                'academic-registrar' => 'Academic Registrar',
                'internal-audit' => 'Internal Audit',
                'planning-and-development' => 'Planning and Development',
                'dean-of-students' => 'Dean of Students',
                'human-resources' => 'Human Resources',
                'research-ethics-committee' => 'Research Ethics Committee',
                'pdu' => 'Procurement and Disposal Unit (PDU)',
            ] as $slug => $label): ?>
                <div class="card" id="<?= esc($slug) ?>">
                    <h3><?= esc($label) ?></h3>
                    <p style="color: var(--gu-muted);">Contact details and services are being finalized for publication here.</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
