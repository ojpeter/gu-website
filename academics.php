<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/gu_services_bridge.php';

$pageTitle = 'Academics';
$pageDescription = 'Faculties, institutes, departments, and programmes at Gulu University.';

/**
 * Matches the slugs already hardcoded in includes/nav.php's mega-menu and
 * index.php's Faculty cards (agriculture-and-environment, law, medicine,
 * peace-and-strategic-studies, ...) so real org_units data can drive this
 * page's anchors without breaking any existing cross-link. Verified against
 * all 8 real seeded faculty/institute/directorate names before relying on
 * it — every one round-trips to the exact slug already in use elsewhere.
 */
function slugify_org_unit_name(string $name): string
{
    $name = preg_replace('/^(Faculty|Institute|Directorate) of /i', '', $name);
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

// Real, operational 3-field search — Keyword / Programme Type / Faculty
// or Institute — backed by gu-services' `programmes`/`org_units`/
// `lookup_values` tables via the read bridge (client instruction,
// 2026-08-09: check gu-services for duplicates first, which is why this
// reuses those existing tables rather than a new local catalog).
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$searchUnitId = isset($_GET['unit']) && $_GET['unit'] !== '' ? (int) $_GET['unit'] : null;
$searchLevel = trim((string) ($_GET['level'] ?? ''));
$isFiltered = $searchQuery !== '' || $searchUnitId !== null || $searchLevel !== '';

$academicUnits = gu_services_academic_units();
$levelOptions = gu_services_lookup_values('level_of_study');

// A real programme's org_unit_id points at the owning *department*, not its
// parent Faculty/Institute — so a Faculty-level search must also match
// every department underneath it, or picking "Faculty of Science" would
// wrongly show zero programmes despite it having three. Resolve the
// selected unit plus its direct children from the tree already in memory.
$searchUnitIds = null;
if ($searchUnitId !== null) {
    $searchUnitIds = [$searchUnitId];
    foreach ($academicUnits as $unit) {
        if ($unit['parent_id'] !== null && (int) $unit['parent_id'] === $searchUnitId) {
            $searchUnitIds[] = (int) $unit['id'];
        }
    }
}

$programmes = gu_services_programmes(
    $searchQuery !== '' ? $searchQuery : null,
    $searchUnitIds,
    $searchLevel !== '' ? $searchLevel : null
);

// Mirrors includes/nav.php's own "Academic Units" mega-menu grouping,
// where the one academic directorate (Directorate of Research and Graduate
// Training) is already presented alongside the Institute, not separately —
// keeps this page's grouping consistent with what the nav already shows.
$topLevelFaculties = array_filter($academicUnits, fn($u) => $u['type'] === 'faculty');
$topLevelInstitutes = array_filter($academicUnits, fn($u) => in_array($u['type'], ['institute', 'directorate'], true));
$searchableUnits = array_filter($academicUnits, fn($u) => in_array($u['type'], ['faculty', 'institute', 'directorate'], true));
$departments = array_filter($academicUnits, fn($u) => $u['type'] === 'department');

require __DIR__ . '/includes/layout_header.php';
?>

<section class="section reveal">
    <div class="container">
        <span class="eyebrow">Academics</span>
        <h1>Academics</h1>
        <p class="lede">Gulu University offers programmes across faculties, institutes, and departments.</p>

        <form action="academics" method="get" class="programme-search__form" style="max-width: 48rem; margin-bottom: var(--gu-space-4);">
            <div class="field">
                <label class="form-label" for="academics-q">Keyword</label>
                <input type="search" id="academics-q" name="q" class="form-control" value="<?= esc($searchQuery) ?>" placeholder="e.g. Computer Science, History&hellip;" maxlength="100">
            </div>
            <div class="field">
                <label class="form-label" for="academics-level">Programme Type</label>
                <select id="academics-level" name="level" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach ($levelOptions as $lvl): ?>
                        <option value="<?= esc($lvl['value']) ?>"<?= $searchLevel === $lvl['value'] ? ' selected' : '' ?>><?= esc($lvl['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label class="form-label" for="academics-unit">Faculty / Institute</label>
                <select id="academics-unit" name="unit" class="form-control">
                    <option value="">All Faculties &amp; Institutes</option>
                    <?php foreach ($searchableUnits as $unit): ?>
                        <option value="<?= esc((string) $unit['id']) ?>"<?= $searchUnitId === (int) $unit['id'] ? ' selected' : '' ?>><?= esc($unit['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><?= nav_icon('study', 16) ?> Search</button>
            <?php if ($isFiltered): ?><a href="academics" class="btn btn-outline">Clear</a><?php endif; ?>
        </form>

        <?php if ($isFiltered): ?>
        <h2>Programmes</h2>
        <?php if ($programmes): ?>
        <div class="card-grid">
            <?php foreach ($programmes as $programme): ?>
                <div class="card">
                    <div class="card-icon"><?= nav_icon('study', 20) ?></div>
                    <p class="card-meta"><?= esc(ucwords(str_replace('_', ' ', $programme['award_level']))) ?> &middot; <?= (int) $programme['duration_years'] ?> years</p>
                    <h3><?= esc($programme['name']) ?></h3>
                    <p style="color: var(--gu-muted);"><?= esc($programme['org_unit_name']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">No programmes matched your search. Try a different keyword, type, or faculty/institute.</div>
        <?php endif; ?>
        <?php endif; ?>

        <h2 style="margin-top: var(--gu-space-5);">Faculties</h2>
        <?php if ($topLevelFaculties): ?>
        <div class="card-grid">
            <?php foreach ($topLevelFaculties as $unit): ?>
                <div class="card" id="<?= esc(slugify_org_unit_name($unit['name'])) ?>">
                    <h3><?= esc($unit['name']) ?></h3>
                    <p class="card-meta">Faculty</p>
                    <p style="color: var(--gu-muted);">Programme listings for this faculty are being finalized and will be published here.</p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">Faculty data is temporarily unavailable.</div>
        <?php endif; ?>

        <?php if ($departments): ?>
        <h2 style="margin-top: var(--gu-space-5);">Departments</h2>
        <div class="card-grid">
            <?php foreach ($departments as $unit): ?>
                <div class="card" id="<?= esc(slugify_org_unit_name($unit['name'])) ?>"><h3><?= esc($unit['name']) ?></h3></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($topLevelInstitutes): ?>
        <h2 style="margin-top: var(--gu-space-5);">Institutes &amp; Directorates</h2>
        <div class="card-grid">
            <?php foreach ($topLevelInstitutes as $unit): ?>
                <div class="card" id="<?= esc(slugify_org_unit_name($unit['name'])) ?>"><h3><?= esc($unit['name']) ?></h3></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h2 style="margin-top: var(--gu-space-5);" id="calendar">Academic Calendar</h2>
        <div class="empty-state">The academic calendar will be published here once the shared source-of-truth integration with the university's eServices systems is in place.</div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout_footer.php'; ?>
