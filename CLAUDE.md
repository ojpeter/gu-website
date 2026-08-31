# Gulu University Public Website — Project Rules (Claude Code reads this automatically every session)

These rules apply to **every task, every session, for the entire lifetime of this project**. They are engineering standards, not suggestions. If a new session starts (context reset, new terminal, new day), re-read this file plus `progress.md` before writing any code.

**What this project is:** the full, from-scratch redesign and build of Gulu University's public website (replacing the current WordPress site at gu.ac.ug) — the marketing/information front door for prospective and current students, staff, researchers, media, donors, and the public. It needs its own design system, its own analytics, its own security hardening, and its own accessibility program, built out fully per `requirements.md` — not treated as an afterthought companion to the eServices Portal.

**Architecture pivot, 2026-08-09 (client instruction) — CMS content management is NOT built here.** The CMS admin panel (News, Events, "GU at a Glance" Site Facts, and self-service Staff Profiles with publications/supervisions) lives inside `gu-services` as its own Portal module, **GU-CMS**, reusing that Portal's shared `users`/`roles`/`permissions` RBAC core — two real roles there, **Communications Officer** (submits content on behalf of any Office/Directorate/Faculty/Campus, cannot publish) and **University PRO** (the sole, university-wide approver). This project's own earlier `admin_users`/`news`/`events`/`site_facts` tables and its `includes/auth.php`/`admin/login.php` groundwork are **abandoned, not extended** — see `progress.md` for the full history. **What this project still owns**: reading published content back out of `gu-services`'s database (see Rule 9) and rendering it on the public pages — that read side has not been built yet.

**Sibling project, not the same project:** `gu-services` (Gulu University eServices Portal — HR, Document Management, E-Voting, Admissions, Fees, Student/Staff portals, GU-CMS, and more) lives alongside this one in the same `htdocs` root. gu-website **reads** a narrow, explicitly-whitelisted slice of `gu-services` data (the live systems/portal directory, the academic calendar, public vacancy postings, public documents, and now the `cms_*` content tables — see `requirements.md` Section 3.3) through one isolated bridge module. It **never** writes to `gu-services`'s database, never reimplements its transactional logic (admissions applications, fee payment, HR workflows, voting, content authoring/approval), and never shares its RBAC/user tables — GU-CMS's editorial workflow lives entirely on the `gu-services` side. Every other capability on this site — the page templates, public forms (contact/newsletter), search, analytics, the whole public rendering layer — is gu-website's own to build, fully, end to end.

**Stack:** Plain PHP (8.x) + MySQL (PDO), no framework — chosen deliberately for consistency with `gu-services` (same skillset, same operational model, no framework upgrade treadmill) and because the brief calls for HTML + PHP directly. Server-rendered templates, a hand-built CSS design system, minimal vanilla JS for progressive enhancement — no SPA framework (Section 11 of `requirements.md` explains the performance rationale).
**Brand palette (shared with `gu-services`, do not invent a new one):** Primary Green `#00BF63`, Accent/Warning Yellow `#FFF24D` (dark text only — never white-on-yellow, it fails contrast), Danger/Alert Red `#FF3131` (reserved for genuinely urgent states only, so it keeps its meaning).
**Full functional spec:** `requirements.md` — 22 sections covering IA, every content section, CMS, UI/UX design system, data & analytics, performance/SEO, cybersecurity, data protection/DPPA-2019 compliance, accessibility, technical architecture, migration, QA, governance, risks, and a phased roadmap. Read it before starting any new feature area — this file (`CLAUDE.md`) covers *how* to build, `requirements.md` covers *what* to build and *why*.

---

## Rule 1 — This Is a Full Build, Not a Brochure Site

Every section in `requirements.md` (Academics, Admissions, Research, News & Events, Community Engagement, Careers, Press/Media, Search, Contact/Directory, the eServices directory) is a real, fully-featured feature area — not a static one-off page. As of the 2026-08-09 pivot (see above), content authored through a CMS (News, Events, Site Facts, Staff Profiles) is managed on the `gu-services` side (GU-CMS) and read here read-only (Rule 9) — do not build a competing admin CRUD for these on this project. Sections that are genuinely this project's own (public forms, search, page templates, analytics) still get built as real, full features, not hardcoded stand-ins. If a requirement in `requirements.md` looks small, check whether it's actually CMS-managed-on-`gu-services`, analytics-tracked, or accessibility/security-sensitive before treating it as a quick static page.

## Rule 2 — Rate Limiting

- Every route that accepts user input or triggers a costly operation (contact form, newsletter signup, "Report a Concern" form, site search, any future CV/application-adjacent upload, admin login, password reset, report/export generation) must be rate-limited.
- Build (or port from `gu-services`'s equivalent pattern) a DB-backed limiter — `includes/ratelimit.php` with a `rate_limit_check()`/`enforce_rate_limit()` pair — rather than ad-hoc per-page counters.
- Log throttle violations (don't silently 429) for later review.
- Minimum coverage at launch: admin login, password reset, contact/newsletter/concern forms, site search, and any bulk export in the admin CMS.

## Rule 3 — Query Design & Optimization

- Every query uses PDO **prepared statements with bound parameters**, via a single shared `db()` connection helper in `config/db.php` — never string-concatenated SQL.
- Index every foreign key and every column used in `WHERE`/`ORDER BY`/`JOIN` — news/events listings, faculty/programme lookups, and search all depend on this.
- Avoid N+1 queries on listing pages (faculty→departments→programmes, news with tags/categories) — use explicit `JOIN`s.
- Paginate every list view server-side (news archive, events archive, search results, admin CMS lists) — never load a full table into a view.
- No migration tool: apply schema changes directly and **also** check in a `database/schema_*.sql` file per logical area (matching `gu-services`'s convention) so the schema stays reproducible from source control.
- `EXPLAIN` any query touching the highest-traffic tables (`news`, `events`, `programmes`, and the `gu_services_bridge` read queries) before merging.

## Rule 4 — Form Input Validation

- Every form submission — public (contact, newsletter, concern-report, search) and admin CMS (every content type's create/edit form) — goes through one shared `Validator` class in `includes/validation.php`. No scattered manual `if` checks.
- Enforce validation **server-side** regardless of any client-side JS validation.
- Specifically:
  - **Dates**: valid format + logical range (event start ≤ end, publish date not absurdly in the past/future for scheduled content).
  - **File uploads** (CMS media, any future document/CV upload): extension whitelist, max size, and **real server-side MIME re-validation via `finfo`** — never trust the client-reported MIME type. This matters more here than almost anywhere else in the project, since uploaded media is public-facing the moment it's published.
  - **Enum-like fields** (content status, event category, faculty selector): validate against the actual lookup table/array, not free text.
  - **Email fields** (contact, newsletter): real format validation, and treat the value as untrusted input all the way through to wherever it's stored/displayed/emailed.
- On validation failure, re-populate the form with submitted values — never lose user input, public or admin.

## Rule 5 — SQL Injection, XSS, CSRF & Core Security Remedies

- **SQL Injection**: PDO prepared statements everywhere, via `db()`. Never interpolate variables into SQL strings.
- **XSS**: escape all dynamic output — CMS-authored content included — with a shared `esc()` helper in `includes/security.php`. Sanitize rich-text CMS content on save *and* escape appropriately on output; never trust that "it came from our own CMS" makes it safe to print raw.
- **CSRF**: every state-changing form (public and admin) includes a CSRF token (`csrf_field()`) and every handler verifies it (`verify_csrf()`) before touching the database.
- **Authentication**: this project has no admin accounts of its own since the 2026-08-09 pivot (Rule 8/9) — CMS authentication (bcrypt, session regeneration, RBAC) is `gu-services`'s responsibility for GU-CMS. If this project ever grows its own authenticated area again (e.g. for something outside CMS scope), apply the same standard: `password_hash()`, `regenerate_session()` on login/privilege change, and 2FA for any high-privilege role, matching the reasoning that a compromised public-website account is an immediate, publicly-visible defacement risk.
- **Authorization**: n/a on this project for CMS content (enforced on the `gu-services` side, Rule 9) — but any public form handler here (contact, newsletter) still needs its own CSRF/validation/rate-limit checks per this rule's other bullets.
- **Session security**: `start_secure_session()` (secure/httponly/samesite cookies, custom cookie name, inactivity timeout) and `destroy_session()` on logout — never call `session_start()` directly.
- **Mass assignment**: explicit column whitelists on every INSERT/UPDATE — never loop over `$_POST` and bind every key blindly.
- **File security**: any elevated-sensitivity upload (e.g. a "Report a Concern" attachment, if ever added) is stored outside the public webroot and served only through a permission-checked streaming script — never a direct static URL. Treat this the same way `gu-services` treats confidential HR files.
- **Audit logging**: every admin publish/edit/delete/permission-change action is written to an append-only audit log (`includes/audit.php`) — who, what, when. This is what makes "a wrong fact got published" or "who deleted this page" answerable after the fact.
- **Error handling**: never expose stack traces, DB errors, or file paths to a visitor. `APP_ENV` gates verbose logging to a private log file only.
- **Dependency hygiene**: this app has no Composer dependencies by design, matching `gu-services`. If one is ever genuinely needed, it goes through explicit review and stays patched — don't add dependencies casually just because "it's easier."
- **Security headers & transport**: CSP, `X-Content-Type-Options`, `X-Frame-Options`/`frame-ancestors`, `Referrer-Policy`, `Permissions-Policy`, HSTS — set deliberately and tested against the site's actual asset/script list, not copy-pasted blind.
- **`security.txt`**: publish one, with a real vulnerability-disclosure contact, before launch.

## Rule 6 — Accessibility Is Non-Negotiable (WCAG 2.2 AA)

This rule exists because this project is public-facing at university scale and directly ties to Gulu University's own community-transformation/inclusion mission — it is not a generic legal-compliance checkbox.

- Semantic HTML, correct heading hierarchy, ARIA landmarks, visible keyboard focus states, full keyboard operability, skip-to-content links — on every page template, from the first one built.
- Alt text is a **required** field for every image — enforced in GU-CMS's own forms on the `gu-services` side (Rule 8/9) for CMS content, and in any image upload this project handles itself.
- Verify color contrast programmatically for every design-token combination before using it — the yellow accent (`#FFF24D`) pairs with dark text only.
- Respect `prefers-reduced-motion` for every animation/transition.
- Run automated accessibility scanning (axe-core/Lighthouse) as part of every release, and do a real manual keyboard + screen-reader pass before major releases — automated tools alone catch roughly a third of real issues.
- Publish and maintain a real Accessibility Statement page with a working contact for reporting barriers.

## Rule 7 — Performance & SEO Are Load-Bearing Requirements

- **Never write a root-absolute internal path** (`href="/about.php"`, `src="/assets/css/style.css"`) — write it relative, without the leading slash (`href="about.php"`, `src="assets/css/style.css"`), full stop. `layout_header.php` sets `<base href="<?= APP_BASE_URL ?>">`, which is what makes every relative path resolve correctly whether this site is deployed at a domain root (production) or nested under a subfolder (this XAMPP dev environment) — root-absolute paths bypass `<base>` entirely and silently break in the nested case (a real, session-spanning bug found only by an actual Lighthouse/browser run on 2026-08-09 — see `progress.md` — because `curl`-based verification never exercises how a browser resolves the links *inside* a rendered page). Two things do NOT work safely under `<base>` and need explicit handling instead of a bare relative path: a query-only href (`?page=2`) must be written with its filename included (`news.php?page=2`), and a bare-fragment href (`#some-id`) resolves against the base URL's path, not the current page — the skip-link handles this via a JS `preventDefault()` handler in `main.js` rather than plain navigation; don't add a second bare-fragment link without the same treatment (or route it through JS the same way).
- Core Web Vitals budget: LCP < 2.5s, INP < 200ms, CLS < 0.1 on 4G-throttled mobile, checked on the homepage and top-traffic pages before every release — a large share of GU's real audience is mobile-first on constrained connections, so this isn't a nice-to-have.
- Responsive images (`srcset`, WebP/AVIF with fallback), lazy-loading below the fold, cache-busted static assets (`?v=filemtime()`, matching `gu-services`'s convention).
- Clean semantic URLs, `schema.org` structured data (`EducationalOrganization`, `Event`, `JobPosting`, `Article`), an auto-generated XML sitemap, and a deliberately-reviewed `robots.txt` — not WordPress defaults inherited by habit.
- Server-side/fragment caching for high-traffic, low-change pages (home, faculty pages).
- **SEO is a standing requirement, not a launch-week task** — a permanent user directive, applied to every page and every future GU web property, not just this one. GU-CMS's own admin forms (`gu-services` side) must have required title/meta-description fields per content type, and every image upload must require real alt text — do not ship a content type without these. Verify against `requirements.md` Section 12's full checklist (pillars → on/off-page/technical/local optimization → the 8-item checklist) before treating any section's SEO as done, not an ad hoc subset of it.

## Rule 8 — CMS Content Lives on `gu-services` (GU-CMS), This Project Only Reads It

- **Do not build an admin CMS, editorial roles, or content CRUD in this project.** As of 2026-08-09 (client instruction), all of that lives inside `gu-services` as its own Portal module, GU-CMS (`gu-services/cms/`), on that Portal's shared RBAC core. Two real roles there: **Communications Officer** (submits News/Events/Site Facts drafts on behalf of any Office/Directorate/Faculty/Campus, cannot publish) and **University PRO** (the sole, university-wide approver — reviews and publishes everything anyone submits). Staff Profiles (bio, specialization, publications, supervisions) are self-service there too — any staff member edits their own profile, University PRO approves it before it's public.
- This project's earlier `admin_users`/`news`/`news_revisions`/`events`/`events_revisions`/`site_facts` tables (in this project's own `gu_website` database) are **dead — do not extend them.** The real, live content tables are `gu-services`'s `gu_hrms` database: `cms_news`/`cms_news_revisions`, `cms_events`/`cms_events_revisions`, `cms_site_facts`, `cms_staff_profiles`/`_revisions`, `cms_staff_publications`, `cms_staff_supervisions`.
- What this project still owns: **rendering** that content publicly. Build the read side (Rule 9) rather than any authoring UI.
- The event de-duplication fix (the known live-site "duplicate Mature Age Entry Exams" bug) is already enforced in `gu-services`'s `cms_events` schema (a unique key on title/start_datetime/location) — nothing to redo here.
- Never render a statistic with no verified, published source — if the `cms_site_facts` read comes back empty, omit the facts section entirely rather than showing a placeholder zero (unchanged from this project's original anti-placeholder principle, just now sourced externally).

## Rule 9 — The `gu-services` Integration: eServices Link + Content Read Bridge, Both Done

- **eServices entry point (done):** one prominent nav/homepage link, pointed at a single config value — `ESERVICES_URL` in `.env` — set to `http://localhost/gu-services` in dev and `https://eservices.gu.ac.ug` in production (confirmed 2026-08-09, `requirements.md` Section 3.6). Never hardcode this URL inline in a template; read it from config so the dev→prod switch is a one-line change. The `gu-services` project's own landing page (`index.php`) is already the real "Portal directory" — it lists every system with live status; do not rebuild that logic here.
- **Content read bridge (done and verified 2026-08-09):** `includes/gu_services_bridge.php` is the ONE place that reads `gu-services`'s `cms_news`/`cms_events`/`cms_site_facts`/`cms_staff_profiles`(+publications/supervisions) tables — never write raw cross-database SQL anywhere else. Decision made and built: a dedicated, least-privilege MySQL account (`gu_website_reader`@`localhost`, credentials in `.env` as `GU_SERVICES_DB_*`) with `GRANT SELECT` on exactly those 8 tables and nothing else — verified directly (not just asserted) that it cannot write and cannot read any other table. Every function in the bridge fails soft (returns `null`/`[]`, logs the error) if `gu-services`'s DB is unreachable — the public site must never crash because of this dependency. `gu_services_asset_url()` resolves uploaded-media paths (News images, Staff photos) against `GU_SERVICES_BASE_URL`. `index.php`, `news.php`, `events.php`, `news-article.php`, `event.php`, `staff-directory.php`, and `sitemap.php` all consume this bridge — do not add a second way to reach `gu-services`'s data.
- If real hosting later puts the two databases on different servers, revisit whether the DB-account approach still holds or an HTTP JSON endpoint on the `gu-services` side becomes necessary — that would be a deliberate architecture change, not a silent one.
- Also whitelisted for later, unrelated to CMS content and not yet built: the `systems` catalog (for a future live per-system eServices directory showing online/offline status), working-calendar/holidays data, publicly-flagged recruitment/vacancy rows, publicly-flagged GU-DMS documents. These would extend the same bridge module, not create a new one.
- This bridge is explicitly in-scope for every security review (Rule 5) — a vulnerability there affects both systems at once. Any `gu-services` schema change that would affect it is a cross-project event: note it in both projects' `progress.md` files, not just one.

## Rule 10 — Data Protection (Uganda DPPA 2019)

- Before building any form that collects personal data, check `requirements.md` Section 14's data-flow table and add the new flow to it if it's not already there — do not add a data-collecting feature without first stating its lawful basis and retention period.
- Cookie/analytics consent must be real and technically enforced — no analytics tag fires before consent is given, and "reject" must actually reject, not just hide the banner.
- The "Report a Concern" channel (safeguarding/grievance) is the single most sensitive feature on this site — treat any work on it with disproportionate care relative to how simple a "contact form" sounds: elevated storage protection, restricted access, and a defined handling process before it ships, not after.
- Never expose HR's confidential employee schema through the public staff directory — the read bridge (Rule 9) only ever selects from `gu-services`'s `cms_staff_profiles` (+ publications/supervisions), a purpose-built, self-service, University-PRO-approved public-profile table, never HR's own confidential employee records.

## Rule 11 — progress.md (mandatory, every session)

Maintain `progress.md` at the project root. This is how a new session (fresh context) picks up exactly where the last one left off — **always read it before starting work, and always update it before ending a session or completing a task.**

`progress.md` must contain:
1. **These 11 rules**, copied in (so it's self-contained even if this file isn't read for some reason).
2. **Completed tasks log** — dated entries, one line per completed item, tagged with which `requirements.md` section/feature area it belongs to.
3. **Pending/in-progress tasks** — a running to-do list, organized by the phases in `requirements.md` Section 21.
4. **Known issues/open questions** — anything blocked on a client decision (start from `requirements.md` Section 22's open-questions list).
5. **Last updated timestamp** and a one-line "where to resume" note at the top, so a new session can read the top few lines and immediately know current state.
6. **Cross-project notes** — anything relevant to `gu-services` (bridge/schema changes, shared-asset updates) that a session on *either* project needs to know about.

Update `progress.md` after every meaningful chunk of work, not just at the end of a whole feature.
