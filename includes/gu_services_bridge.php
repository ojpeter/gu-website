<?php

require_once __DIR__ . '/../config/config.php';

/**
 * The ONE seam through which this project reads gu-services' data
 * (requirements.md Section 3.3, CLAUDE.md Rule 9). Read-only, least-
 * privilege: the DB account behind this connection has SELECT granted on
 * exactly the cms_* content tables and nothing else (verified — it cannot
 * read `users`, cannot write anything). Never write through this
 * connection, never query a table outside the whitelist below, and never
 * construct SQL here from raw request input beyond a bound parameter.
 *
 * Whitelisted read targets: cms_news(+revisions), cms_events(+revisions),
 * cms_site_facts, cms_staff_profiles, cms_staff_publications,
 * cms_staff_supervisions, org_units, programmes, courses, programme_courses,
 * lookup_values (the last 5 added 2026-08-09 for the real Search-for-a-
 * Programme feature — shared-core reference tables also used by E-Voting/HR
 * on the gu-services side, not CMS-authored content, but read the same
 * read-only way).
 *
 * Fails soft: if gu-services' database is unreachable, every function here
 * returns an empty result rather than crashing the public site (Section 11
 * "graceful degradation") — the error is logged, never shown to a visitor.
 */
function gu_services_db(): ?PDO
{
    static $pdo = null;
    static $failed = false;

    if ($pdo instanceof PDO) {
        return $pdo;
    }
    if ($failed) {
        return null;
    }

    $host = env('GU_SERVICES_DB_HOST', 'localhost');
    $name = env('GU_SERVICES_DB_NAME', '');
    $user = env('GU_SERVICES_DB_USER', '');
    $pass = env('GU_SERVICES_DB_PASS', '');
    $charset = env('GU_SERVICES_DB_CHARSET', 'utf8mb4');

    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$name};charset={$charset}",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log('gu_services_bridge: connection failed: ' . $e->getMessage());
        $failed = true;
        return null;
    }
}

/** Builds a full URL to a gu-services-hosted static asset (uploaded images). */
function gu_services_asset_url(?string $relativePath): ?string
{
    if (!$relativePath) {
        return null;
    }
    return GU_SERVICES_BASE_URL . ltrim($relativePath, '/');
}

/**
 * @return array<int,array> Published news, newest first.
 */
function gu_services_published_news(?int $limit = null, int $offset = 0): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    $sql = "SELECT id, title, slug, excerpt, featured_image, featured_image_alt, meta_title, meta_description, published_at, updated_at
            FROM cms_news WHERE status = 'published' ORDER BY published_at DESC";
    if ($limit !== null) {
        $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
    }
    try {
        return $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: news query failed: ' . $e->getMessage());
        return [];
    }
}

function gu_services_published_news_count(): int
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return 0;
    }
    try {
        return (int) $pdo->query("SELECT COUNT(*) FROM cms_news WHERE status = 'published'")->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function gu_services_news_by_slug(string $slug): ?array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return null;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM cms_news WHERE slug = :slug AND status = 'published'");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (PDOException $e) {
        error_log('gu_services_bridge: news_by_slug failed: ' . $e->getMessage());
        return null;
    }
}

/**
 * @return array<int,array> Published, upcoming (or all, if $upcomingOnly is
 *   false) events, soonest first.
 */
function gu_services_published_events(bool $upcomingOnly = true, ?int $limit = null, int $offset = 0): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    $sql = "SELECT id, title, slug, description, location, start_datetime, end_datetime, updated_at
            FROM cms_events WHERE status = 'published'";
    if ($upcomingOnly) {
        $sql .= ' AND start_datetime >= NOW()';
    }
    $sql .= ' ORDER BY start_datetime ASC';
    if ($limit !== null) {
        $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
    }
    try {
        return $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: events query failed: ' . $e->getMessage());
        return [];
    }
}

function gu_services_published_events_count(bool $upcomingOnly = true): int
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return 0;
    }
    $sql = "SELECT COUNT(*) FROM cms_events WHERE status = 'published'" . ($upcomingOnly ? ' AND start_datetime >= NOW()' : '');
    try {
        return (int) $pdo->query($sql)->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function gu_services_event_by_slug(string $slug): ?array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return null;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM cms_events WHERE slug = :slug AND status = 'published'");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    } catch (PDOException $e) {
        error_log('gu_services_bridge: event_by_slug failed: ' . $e->getMessage());
        return null;
    }
}

/**
 * @return array<int,array> Published "GU at a Glance" facts. Never
 *   fabricated/defaulted — an unreachable bridge or an empty table both
 *   correctly result in an empty array, which callers must render as "no
 *   facts section" rather than a placeholder zero (requirements.md Section 10.3).
 */
function gu_services_published_facts(): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        return $pdo->query('SELECT label, value, as_of_date FROM cms_site_facts WHERE is_published = 1 ORDER BY sort_order, id')->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: facts query failed: ' . $e->getMessage());
        return [];
    }
}

/**
 * @return array<int,array> Published staff profiles, alphabetical by display name.
 */
function gu_services_published_staff_profiles(): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        return $pdo->query(
            "SELECT id, display_name, title_position, specialization, department_office, bio, photo, photo_alt, public_email, public_phone
             FROM cms_staff_profiles WHERE status = 'published' ORDER BY display_name ASC"
        )->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: staff profiles query failed: ' . $e->getMessage());
        return [];
    }
}

function gu_services_staff_publications(int $profileId): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare('SELECT title, publication_year, publisher_or_journal, url FROM cms_staff_publications WHERE profile_id = :pid ORDER BY publication_year DESC');
        $stmt->execute(['pid' => $profileId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function gu_services_staff_supervisions(int $profileId): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare('SELECT student_name, degree_level, topic, status, supervision_year FROM cms_staff_supervisions WHERE profile_id = :pid ORDER BY supervision_year DESC');
        $stmt->execute(['pid' => $profileId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * @return array<int,array> Real, live lookup options for one enum-like
 *   category (e.g. 'level_of_study') — used to populate the homepage's
 *   "Programme Type" select from real governed data rather than a
 *   hardcoded PHP array, matching Rule 3's "enum fields validated against
 *   a real lookup table, not free text" standard.
 */
function gu_services_lookup_values(string $category): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare('SELECT value, label FROM lookup_values WHERE category = :category AND is_active = 1 ORDER BY sort_order, label');
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: lookup_values query failed: ' . $e->getMessage());
        return [];
    }
}

/**
 * @return array<int,array> Academic organizational units (Faculties,
 *   Institutes, Departments — org_units.is_academic = 1), optionally
 *   filtered by a name keyword and/or restricted to one unit id. Top-level
 *   units (Faculties/Institutes, parent_id NULL) sort first.
 */
function gu_services_academic_units(?string $keyword = null, ?int $unitId = null): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    $sql = "SELECT id, parent_id, code, name, type FROM org_units WHERE is_academic = 1 AND status = 'active'";
    $params = [];
    if ($unitId !== null) {
        $sql .= ' AND id = :unit_id';
        $params['unit_id'] = $unitId;
    }
    if ($keyword !== null && $keyword !== '') {
        $sql .= ' AND name LIKE :keyword';
        $params['keyword'] = '%' . $keyword . '%';
    }
    $sql .= ' ORDER BY (parent_id IS NULL) DESC, name ASC';
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: academic_units query failed: ' . $e->getMessage());
        return [];
    }
}

/**
 * @return array<int,array> Active programmes, optionally filtered by a name
 *   keyword, restricted to a set of owning organizational unit ids, and/or
 *   one real `award_level` value (e.g. 'undergraduate' — a genuine
 *   per-programme column, not a guessed faculty-to-level mapping). Each row
 *   includes the owning unit's name for display.
 *
 * $orgUnitIds accepts more than one id deliberately: a real programme's
 * `org_unit_id` points at the owning *department* (e.g. Department of
 * Computer Science), not its parent Faculty — a Faculty-level search needs
 * to match every department underneath it too, or selecting "Faculty of
 * Science" would wrongly return zero programmes despite it having three.
 * Callers resolve the unit-plus-descendants id set themselves (see
 * academics.php) since org_units' tree already lives in memory there.
 */
function gu_services_programmes(?string $keyword = null, ?array $orgUnitIds = null, ?string $awardLevel = null): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    $sql = "SELECT p.id, p.code, p.name, p.award_level, p.duration_years, p.org_unit_id, ou.name AS org_unit_name
            FROM programmes p
            JOIN org_units ou ON ou.id = p.org_unit_id
            WHERE p.is_active = 1";
    $params = [];
    if ($orgUnitIds !== null && $orgUnitIds !== []) {
        $placeholders = [];
        foreach (array_values($orgUnitIds) as $i => $id) {
            $key = 'unit' . $i;
            $placeholders[] = ':' . $key;
            $params[$key] = (int) $id;
        }
        $sql .= ' AND p.org_unit_id IN (' . implode(',', $placeholders) . ')';
    }
    if ($awardLevel !== null && $awardLevel !== '') {
        $sql .= ' AND p.award_level = :award_level';
        $params['award_level'] = $awardLevel;
    }
    if ($keyword !== null && $keyword !== '') {
        $sql .= ' AND p.name LIKE :keyword';
        $params['keyword'] = '%' . $keyword . '%';
    }
    $sql .= ' ORDER BY p.name ASC';
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: programmes query failed: ' . $e->getMessage());
        return [];
    }
}

/**
 * @return array<int,array> Active courses in a programme's curriculum,
 *   ordered by year of study then semester. Empty today — the `courses`
 *   table exists but has zero rows until Registry/Academic Affairs supply
 *   real curriculum data (see database/schema_courses.sql on the
 *   gu-services side — deliberately not seeded with invented course codes).
 */
function gu_services_courses_for_programme(int $programmeId): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare(
            'SELECT c.id, c.code, c.title, c.credit_units, pc.year_of_study, pc.semester, pc.is_core
             FROM programme_courses pc
             JOIN courses c ON c.id = pc.course_id
             WHERE pc.programme_id = :pid AND c.is_active = 1
             ORDER BY pc.year_of_study ASC, pc.semester ASC, c.title ASC'
        );
        $stmt->execute(['pid' => $programmeId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: courses_for_programme query failed: ' . $e->getMessage());
        return [];
    }
}

/**
 * @return array<int,array> Active courses matching a keyword against code
 *   or title — the third leg of the homepage's Keyword/Programme Type/
 *   Faculty-Institute search, once real course data exists.
 */
function gu_services_search_courses(string $keyword): array
{
    $pdo = gu_services_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare(
            'SELECT id, code, title, org_unit_id, credit_units FROM courses
             WHERE is_active = 1 AND (title LIKE :kw OR code LIKE :kw) ORDER BY title ASC'
        );
        $stmt->execute(['kw' => '%' . $keyword . '%']);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('gu_services_bridge: search_courses query failed: ' . $e->getMessage());
        return [];
    }
}
