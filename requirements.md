# Gulu University Public Website — Requirements Document
## State-of-the-Art Redesign of gu.ac.ug

**Client:** Gulu University
**Prepared as:** Functional + non-functional requirements for engineering build-out
**Reviewed from the perspective of:** UI/UX design, data analytics, cybersecurity, and data protection
**Current live site reviewed:** https://gu.ac.ug/ (WordPress)
**Sibling project:** `gu-services` — the Gulu University eServices Portal (plain PHP + MySQL/PDO), located alongside this project in the same `htdocs` root. See Section 3 — this is not a standalone redesign, it is one half of a two-project ecosystem.
**Target stack:** HTML5 + PHP (no framework — see Section 16 and `CLAUDE.md`), MySQL
**Audience:** Engineer(s) implementing this from scratch in Claude Code, session over session

---

## 0. How to use this document

This is a **functional and non-functional requirements spec**, not a finished visual design. It describes *what* the new public website must do and *why*, organized so that a build can proceed in phases (Section 21) without losing coherence across sessions. Pair it with `CLAUDE.md` (engineering rules, read every session) and `progress.md` (state tracking, updated every session).

Four lenses were applied deliberately, per the brief, and each shows up as its own section rather than being scattered as asides:
- **UI/UX** — Section 9
- **Data & Analytics** — Section 10
- **Cybersecurity** — Section 13
- **Data Protection & Privacy** — Section 14

---

## 1. Executive Summary & Vision

Gulu University's current public website (gu.ac.ug, WordPress) functions as a basic institutional brochure but falls well short of what a modern public university needs: it has placeholder statistics ("0 programmes", "0% pass rate"), fragmented portal access (four separate unlinked login systems), no visible design system, no accessibility posture, and no measurable data strategy. It does not reflect the scale of what the university actually runs — six faculties, four extended campuses, a growing eServices Portal (`gu-services`) with 24+ planned digital systems.

The redesign's vision is a **single coherent Gulu University digital front door**: fast, accessible, secure, data-informed, and visually on par with the best modern university websites globally, while being truthful about GU's own identity — "For Community Transformation," rural development, agriculture, peace-building — rather than a generic template. The public website becomes the marketing/information layer; `gu-services` remains the transactional/authenticated layer; the two are visually and architecturally one family, not two disconnected products.

**"Exceed the normal" is operationalized concretely in this document as:**
- Real-time institutional statistics instead of placeholder zeros (Section 10.3)
- A single, brand-consistent design system shared with `gu-services` (Section 9)
- WCAG 2.2 AA accessibility as a hard requirement, not an afterthought (Section 15)
- A security posture equivalent to `gu-services`'s own hardened standard, applied to a public-facing attack surface (Section 13)
- Explicit Uganda Data Protection and Privacy Act 2019 compliance, not just a copy-pasted privacy policy (Section 14)
- A live, read-only integration with `gu-services` so the two systems never drift out of sync (Section 3)

---

## 2. Current State Audit — gu.ac.ug (as reviewed)

| Area | Finding |
|---|---|
| Platform | WordPress (`wp-content/uploads` paths visible), standard theme/plugin structure |
| Navigation | Deep, inconsistent hierarchy — primary nav (Home, About Us, Study, Academic Units, Research, Services, Community Engagement) plus an entirely separate secondary nav (Timetables, Guild, Notice Board, Downloads, Events, News, Library, E-learning, FAQs) with overlapping purposes |
| Portals | Four separate, unlinked login destinations advertised in the footer/header: Student Portal (`myportal.gu.ac.ug`), Staff Portal (`staffportal.gu.ac.ug`), E-Learning (`guele.gu.ac.ug`), Google Workspace mail — no unified "eServices" entry point, no visibility into which systems are actually online |
| "GU at a Glance" stats | Displaying **placeholder zeros** — "Number of study programmes: 0", "Undergraduate pass rate: 0%", "Modules available online: 0%", "Total Alumni: 0+" — a credibility problem for prospective students and accreditation bodies alike |
| Events | Duplicate entries observed (repeated "Mature Age Entry Exams" listings) — no dedupe/QA process evident |
| Admissions | Links out to `apply.gu.ac.ug` — correct architecture (keep this pattern, see Section 3) |
| Accessibility | No evidence of an accessibility statement, skip links, or alt-text discipline; WordPress theme default |
| Mobile | Not verifiable without live rendering, but WordPress theme responsiveness is inconsistent by default and not something to assume |
| SEO/structured data | No visible `EducationalOrganization` schema.org markup; page titles appear generic |
| Security posture | Standard public WordPress exposure (plugin/theme CVEs, `wp-login.php` brute-force surface, XML-RPC) — not evaluated in depth here since the target stack changes entirely (Section 16), but the migration must not inherit WordPress's attack surface |
| Governance content | Present and reasonably complete (Chancellor, VC, Council, Senate, Management) |
| Footer | Four-column structure (About, Quick Links, Useful Links, Contact) — reasonable IA to preserve conceptually |

**Verified institutional facts to carry forward accurately (do not re-guess these):**
- Mission line: **"For Community Transformation"**
- Vice Chancellor: **Prof. Openjuru L. George**
- Six faculties: Agriculture and Environment, Business and Development Studies, Education and Humanities, Law, Medicine, Science
- Specialized units: Institute of Peace and Strategic Studies; Directorate of Research and Graduate Training
- Extended campuses: Kitgum, Kotido, Hoima; Constituent College Moroto
- Address: Gulu University, P.O. Box 166, Laroo Division, Gulu City
- Phone: +256-471-432095, +256-471-432648 · Email: info@gu.ac.ug
- Existing subdomains/portals to preserve or supersede deliberately: `apply.gu.ac.ug`, `myportal.gu.ac.ug`, `staffportal.gu.ac.ug`, `guele.gu.ac.ug`, `ir.gu.ac.ug` (institutional repository), `pay.gu.ac.ug`

All of the above must be confirmed with the Communications Office before launch, not re-scraped from a possibly-stale WordPress site at build time — treat this table as a starting point, not a frozen source of truth.

---

## 3. Relationship to `gu-services` — Integration Architecture (read this before designing anything)

This is the most consequential architectural decision in the whole document, because it determines what gu-website is allowed to build versus what it must link to or read from.

### 3.1 What `gu-services` already is

`gu-services` is the **Gulu University eServices Portal**: one shared PHP/MySQL codebase with a shared RBAC core (`roles`/`permissions`/`role_permissions`/`user_roles`), currently hosting three fully-built systems (**GU-HRM** — HR/payroll/leave/discipline/performance across 11 sub-systems; **GU-DMS** — document management/workflow; **GU-EVoting** — staff/student/senate elections) plus a `systems` catalog table seeding ~24 more planned systems (Admissions, Fees & Payments, Scholarships, Library, Research Repository, University Journal, Biometric Attendance, Graduate Tracking, Course Timetabling, etc.), each with a label, description, category, live-URL, and online/available flags.

Its brand palette is already fixed and live in production code: **Primary Green `#00BF63`, Warning/Accent Yellow `#FFF24D`, Danger Red `#FF3131`.**

### 3.2 What gu-website is (and is not)

gu-website is the **public, unauthenticated marketing and information layer**: prospective students, current students, staff, alumni, donors, media, government/accreditation bodies, and the general public land here first. It answers "what is Gulu University, what can I study, how do I apply, what's happening, who do I contact" — and then hands off to `gu-services` (or its future subdomains) for anything transactional or requiring login.

**gu-website must never:**
- Reimplement admissions application logic, fee payment, leave/HR workflows, election voting, or document routing — these belong to `gu-services` and its constituent systems.
- Maintain its own duplicate copy of the RBAC core, employee records, student records, or election data.
- Write to any `gu-services` database table. It may only **read** from a narrow, explicitly-granted, least-privilege view (Section 3.3).

**gu-website must own:**
- All public informational content: home, about, academic programme catalogue (descriptive, not enrolment), faculties/departments, research showcase, news, events calendar, admissions *information* (not the application form itself), community engagement, governance pages, contact.
- Its own lightweight CMS for that content (Section 8), with its own editorial roles (Communications Office), not the HRM/RBAC roles.
- Public job vacancy listings (read-only display; the actual application still happens through the recruitment system).
- Public downloadable documents (policies, forms, prospectuses) — either self-hosted or, preferably, surfaced from GU-DMS's public document category (Section 3.3) so there is one canonical copy of every policy document, not two.

### 3.3 The integration mechanism — decide once, reuse everywhere

Recommended approach (confirm with client before building, see Section 22):

1. **A dedicated, least-privilege MySQL account** for gu-website that can `SELECT` only from a small, explicit whitelist of `gu-services` tables/views — never the full schema. Candidate read targets:
   - `systems` (and its `is_available`/`status`/`url` columns) → powers a live "eServices" directory on the website instead of a hardcoded, rot-prone list of portal links. When a system goes online in `gu-services`, the website reflects it automatically; when one is taken offline for maintenance, the website doesn't keep advertising a dead link.
   - GU-HRM's public-facing **working calendar / holidays** table → powers the website's academic calendar so there is one source of truth for term dates and public holidays, not two calendars that silently diverge.
   - GU-HRM's **recruitment/vacancy** rows flagged as "publicly advertised" → powers the website's Careers/Vacancies page.
   - GU-DMS's documents flagged **public** (a category, not a blanket read) → powers the website's Downloads/Policies section.
2. Wrap every read behind a small internal PHP data-access layer (e.g. `includes/gu_services_bridge.php`) so the rest of gu-website's code never writes raw cross-database SQL inline — one seam to harden, one seam to change if the schema moves.
3. If MySQL cross-database `SELECT` access is not operationally acceptable (e.g. different DB servers in production), fall back to a tiny internal read-only JSON endpoint inside `gu-services` (e.g. `gu-services/api/public_systems.php`) that gu-website polls/caches — same principle, HTTP instead of a DB link. **Decide which of these two before Phase 2** (Section 21); do not build both.
4. Never expose this bridge to the public internet directly — it is server-to-server only, not a public API, unless a genuine external consumer (e.g. a mobile app) is identified and separately threat-modeled.

### 3.4 Shared identity, separate logins (for now)

Do not attempt a full SSO merge between gu-website and `gu-services` in this phase — that is a significant authentication re-architecture of a live, actively-developed system and is out of scope here. Instead:
- The website's own CMS admin accounts (Communications Office editors) are a **separate, small user table scoped to gu-website only** — do not reuse `gu-services`'s `users`/RBAC tables for this, since website editors are not HR/DMS/EVoting users and giving them any access to that schema, even read-only, is an unnecessary privilege expansion.
- Every portal link surfaced on the public website (Student Portal, Staff Portal, E-Learning, Admissions, Fees) continues to point to its existing subdomain and login page — the website's job is discovery and routing, not authentication.
- Flag full SSO (one GU identity across website CMS + every `gu-services` system) as a **future roadmap item** (Section 21, Phase 5+), not a Phase-1 requirement.

### 3.5 Shared design language, separate codebases

Both projects visually belong to the same institution and must look like it:
- Reuse the exact brand palette (`#00BF63` / `#FFF24D` / `#FF3131`) as CSS custom properties, named identically in both projects' stylesheets, so a future shared "GU Design System" extraction is a lift-and-shift, not a rewrite.
- Reuse the same crest/logo assets, the same base typography stack, and the same favicon family.
- Do **not** literally share a `template/` folder wholesale — `gu-services` is an authenticated dashboard product (Bootstrap admin theme); gu-website is a public marketing site and needs its own front-end architecture (Section 9), just built on the same design tokens.

### 3.6 Confirmed: eServices entry point URL (decided 2026-08-09)

This resolves part of Section 22's original open question #1 and simplifies Section 7.7:

- **Development:** the single "eServices" link/CTA on the public website points to `http://localhost/gu-services` (the `gu-services` project's own landing page, `index.php` — already documented in `gu-services`'s `CLAUDE.md` as "the true entry point — it lists every system and is where login lands").
- **Production:** the same link points to `https://eservices.gu.ac.ug` — a dedicated subdomain for the whole eServices Portal landing page, superseding the current live site's scattered, separately-advertised `myportal.gu.ac.ug` / `staffportal.gu.ac.ug` / `apply.gu.ac.ug` / `guele.gu.ac.ug` links (Section 2). Those individual systems remain reachable — but discovery now goes through one consolidated portal landing page, not four independent footer buttons.
- **Config, not a hardcoded string:** store this as a single environment value (e.g. `ESERVICES_URL` in `.env`/`config/config.php`) so switching between the dev and prod value is a one-line config change, never a find-and-replace across templates. See Section 16.
- **Sequencing:** the website is being built **first**, ahead of any deeper `gu-services` bridge work (Section 3.3's live systems-table sync, academic calendar sync, etc.). Phase 1 of this project therefore only needs this **one static, config-driven outbound link** — not the live per-system status directory described in Section 7.7. The live directory (rendering each system's real online/offline status from `gu-services`'s `systems` table) is explicitly deferred to a later phase, once both projects are far enough along to integrate safely (Section 21). Do not block Phase 1 website delivery on the bridge being built.

---

## 4. Goals & Success Criteria

| Goal | Success Criteria |
|---|---|
| Credible, accurate public information | Zero placeholder statistics at launch; all faculty/programme/contact data verified against a named institutional source |
| Modern, professional visual identity | A documented design system (Section 9) applied consistently across 100% of pages; passes an external design review |
| Fast | Core Web Vitals "Good" on all three metrics (LCP < 2.5s, INP < 200ms, CLS < 0.1) on the homepage and top 10 landing pages, mobile 4G throttled |
| Accessible | WCAG 2.2 Level AA conformance, verified by both automated (axe/Lighthouse) and manual keyboard/screen-reader testing |
| Secure | Zero critical/high findings on an OWASP-Top-10-aligned review at launch (Section 13); a working vulnerability disclosure process live from day one |
| Privacy-compliant | A DPA-2019-aligned privacy policy live, a data inventory completed, cookie consent implemented, before any personal data is collected on the new site |
| Data-informed | GA4 (or equivalent) live from day one, with a defined KPI dashboard reviewed monthly by Communications (Section 10) |
| Coherent with `gu-services` | Live eServices directory sourced from `gu-services`'s own `systems` table, zero manually-maintained duplicate portal-link lists left in the codebase after Phase 2 |
| Inclusive of GU's actual mission | Content architecture foregrounds rural development, agriculture, peace-building, and community transformation — not a generic "any university" template |

---

## 5. Users & Personas

| Persona | Primary needs | Notes |
|---|---|---|
| Prospective undergraduate (Uganda) | Programmes offered, fees, entry requirements, how to apply, campus life, scholarships | Often on a low-bandwidth mobile connection — performance budget matters most here |
| Prospective postgraduate/international student | Research strengths, supervisors, postgraduate programmes, visa/international-student guidance, fees in relatable terms | Needs credible research output showcase (Section 7.5) |
| Current student | Timetables, academic calendar, e-learning, guild, notice board, downloads, exam results status | Mostly routed to `myportal`/`guele` — website's job is fast discovery, not duplicating the portal |
| Current/prospective staff | Vacancies, staff portal, HR policies, payslip/portal access, internal circulars | Routed to `staffportal`; website surfaces public vacancy postings (Section 3.2) |
| Parent/guardian | Fees, safety, accommodation, admissions process, contact channels | Needs plain-language, trustworthy framing, not jargon |
| Researcher/academic partner | Research areas, publications, institutional repository, partnership/collaboration contacts | Needs an actual research showcase, not just a "Research" nav label |
| Media/journalist | Press releases, media kit, VC/spokesperson contact, verified statistics | Needs a dedicated Press/Media page (Section 7.9) |
| Donor/development partner | Impact stories, community transformation projects, how to give, transparency/accountability | Underserved on the current site — a real gap to close |
| Person with a disability | Full site usability via keyboard/screen reader, an accessibility statement with a real contact for reporting barriers | Directly tied to GU's own community-transformation/inclusion mission — not optional |
| Government/accreditation/NCHE reviewer | Verifiable programme lists, governance structure, compliance documents | Needs the Downloads/Policies section (Section 3.2/3.3) to be authoritative, not stale |
| Alumni | Alumni network, giving back, tracing/graduate outcomes | Ties to `gu-services`'s planned Graduate Tracking systems — informational only on the website for now |

---

## 6. Information Architecture / Sitemap

```
Home
├── About
│   ├── Overview & Mission ("For Community Transformation")
│   ├── History
│   ├── Governance (Chancellor, VC, Council, Senate, Management)
│   ├── Campuses (Main — Gulu; Kitgum; Kotido; Hoima; Moroto Constituent College)
│   ├── Facts & Figures (live stats, Section 10.3)
│   ├── Accreditation & Quality Assurance
│   └── Careers at GU (public vacancy listings, Section 3.2)
├── Academics
│   ├── Faculty of Agriculture and Environment
│   ├── Faculty of Business and Development Studies
│   ├── Faculty of Education and Humanities
│   ├── Faculty of Law
│   ├── Faculty of Medicine
│   ├── Faculty of Science
│   ├── Institute of Peace and Strategic Studies
│   ├── Directorate of Research and Graduate Training
│   ├── Academic Calendar (sourced from gu-services working calendar, Section 3.3)
│   ├── E-Learning (link out to guele.gu.ac.ug)
│   └── Library (link out / showcase)
├── Admissions
│   ├── Undergraduate Admissions
│   ├── Postgraduate Admissions
│   ├── International Students
│   ├── Fees & Scholarships
│   ├── How to Apply (CTA → apply.gu.ac.ug)
│   └── FAQs
├── Research
│   ├── Research Areas & Priorities
│   ├── Publications & Institutional Repository (link → ir.gu.ac.ug)
│   ├── Research Ethics
│   ├── Partnerships & Collaboration
│   └── Grants & Funding Opportunities
├── Student Life
│   ├── Campus Life & Facilities
│   ├── Guild / Student Government
│   ├── Sports & Recreation
│   ├── Accommodation
│   ├── Health & Wellbeing / Safeguarding
│   └── Notice Board
├── Community Engagement
│   ├── Outreach & Rural Development Projects
│   ├── Partnerships (government, NGOs, private sector)
│   └── Impact Stories
├── News & Events
│   ├── News
│   ├── Events Calendar
│   └── Press & Media Kit
├── eServices (NEW — replaces scattered portal links)
│   └── Phase 1: single link to the eServices Portal landing page (`eservices.gu.ac.ug` / `localhost/gu-services` in dev, Section 3.6). Phase 2+: live directory sourced from gu-services `systems` table (Section 3.3), grouped by audience, showing live status
├── Give / Support GU
│   └── Alumni giving, donor information, transparency
├── Contact
│   ├── General Contact / Campus Map
│   ├── Directory (departments/offices)
│   └── Report a Concern / Whistleblowing
├── Accessibility Statement
├── Privacy Policy / Data Protection
└── Search (site-wide, Section 7.10)
```

This collapses the current site's two overlapping navigation systems (primary + secondary "quick links" bar) into **one coherent structure with a single new "eServices" hub**, rather than perpetuating four unlinked portal buttons scattered across header/footer.

---

## 7. Functional Requirements

### 7.1 Home
- Hero section communicating mission ("For Community Transformation") with real, current imagery — not stock photography.
- Live facts strip (Section 10.3) — programmes count, student population, alumni count, research output — pulled from real data, never hardcoded placeholders.
- Featured news (max 3–4), upcoming events (max 3–4), quick links to Admissions/eServices/Academics.
- A single, prominent "Apply Now" CTA and a single, prominent "eServices" entry point — not four competing portal buttons.
- Emergency/announcement banner capability (dismissible, CMS-controlled) for urgent notices (e.g. closures, safety alerts) — a real gap on the current site.

### 7.2 About / Governance
- Structured profile pages for Chancellor, Vice Chancellor, Council, Senate, Management — each with photo, bio, and (where applicable) a public contact channel, not just a name.
- Campus pages for Kitgum, Kotido, Hoima, and Moroto Constituent College with their own facilities/programme info, not just a paragraph mention.

### 7.3 Academics
- One structured template per faculty/institute (overview, departments, programmes offered, staff directory link, contact) — content-managed, not six hand-built one-off pages that drift out of consistency.
- Programme pages: entry requirements, duration, structure, career outcomes, fees pointer (link to Fees page, don't duplicate the number in two places).
- Academic Calendar page sourced from the shared working-calendar data (Section 3.3) — one calendar, not a second one manually re-typed by Communications.

### 7.4 Admissions
- Clear, separate undergraduate/postgraduate/international tracks.
- A fee estimator or at minimum a clear, current fee schedule (downloadable + on-page table, not PDF-only).
- Scholarship/bursary information with eligibility criteria.
- A single, unambiguous "Apply Now" CTA to `apply.gu.ac.ug` — the website does not build a parallel application form.
- An admissions FAQ, ideally paired with a lightweight FAQ/chat assistant (Section 7.11) trained only on verified GU admissions content — not a general-purpose open chatbot.

### 7.5 Research
- A real research output showcase: searchable/filterable by faculty, keyword, year — pulling at minimum a curated list, ideally syncing later with `ir.gu.ac.ug` and the planned `research_repository`/`university_journal` systems in `gu-services`'s catalog.
- Research ethics and grants information as first-class pages, not buried sub-pages.

### 7.6 News & Events
- A real CMS-backed news system (categories, tags, author/attribution, publish scheduling) — replacing ad hoc WordPress posts.
- An events calendar with **deduplication built into the data model** (unique constraint on title+date+location or an explicit "recurring event" concept) — directly fixing the duplicate "Mature Age Entry Exams" bug observed on the current site.
- iCal export for events (a genuinely useful, commonly-expected feature that's cheap to add).

### 7.7 eServices Entry Point
- **Phase 1 (build now):** one clear, prominent "eServices" link in the primary nav and homepage — a single static, config-driven URL (Section 3.6) pointing to the `gu-services` Portal landing page (`http://localhost/gu-services` in dev, `https://eservices.gu.ac.ug` in production). This alone already fixes the current live site's problem of four separate, unlinked portal buttons — one consolidated destination, not a rebuild of the directory logic on the website side.
- **Phase 2+ (deferred, per Section 3.6):** upgrade this from a single link into a grouped, live-rendered directory (by audience — Students/Staff/Applicants/Researchers/Public — and category) sourced from `gu-services`'s `systems` table via the integration bridge (Section 3.3), with a live/offline or "coming soon" badge per entry. Not required for launch; the `gu-services` Portal landing page already performs this job on its own side in the meantime.

### 7.8 Careers / Vacancies
- Public listing of vacancies flagged as externally-advertised in `gu-services`'s recruitment module (read-only display, Section 3.3) — application still happens through the proper HR channel/portal, not a second parallel form on the website.

### 7.9 Press & Media
- Press releases, a downloadable media kit (logo files, brand guidelines, official photography), and a named media contact — this is a genuine gap on the current site and matters for reputation management.

### 7.10 Site-Wide Search
- Fast, relevant search across news, events, programmes, staff directory, and downloadable documents — with query analytics feeding Section 10 (what people search for but don't find is a direct signal for missing content).

### 7.11 Optional AI-Assisted Admissions FAQ (recommended "exceed the normal" feature)
- A constrained assistant answering only from a curated, versioned knowledge base of GU's own admissions/programme content (retrieval-based, not open internet) — reduces load on Admissions staff for repetitive questions, with a clear escalation path to a human/contact form for anything it can't answer confidently. Flagged as a Phase 3+ enhancement (Section 21), not a Phase-1 blocker, and must not collect or infer personal data beyond what's needed to answer the question (Section 14).

### 7.12 Multilingual Consideration
- English as the primary language (matches current site and Uganda's official language of instruction). Evaluate adding a Luo/Acholi content layer for key public-facing pages (About, Admissions overview, key announcements) as a Phase 4+ community-engagement enhancement — confirm scope/appetite with Communications before committing (Section 22).

### 7.13 Contact & Directory
- A real, searchable staff/department directory (name, role, department, official email/phone) — distinct from the confidential HR records in `gu-services`; only publicly-appropriate fields are ever exposed here, sourced from a small curated public-directory table maintained by Communications/HR jointly, not a live read of HR's confidential employee schema.
- A "Report a Concern" channel (safeguarding, grievance, whistleblowing) with a clear statement on confidentiality and where reports actually go — do not build this as a naive public contact form without a defined, secure handling process (Section 13/14).

---

## 8. CMS & Content Management Requirements

- A purpose-built, lightweight admin CMS (plain PHP, matching Section 16 stack) for: News, Events, Vacancies (read-only mirror, not authored here), Faculty/Programme pages, Staff Directory entries, Downloads/Documents, Homepage feature slots, Announcement banner.
- Editorial roles: **Content Editor** (draft/edit), **Content Approver** (publish) — a lightweight two-role workflow, not the full 28-role RBAC complexity of `gu-services` (Section 3.4) — appropriate to a marketing CMS, not an HR system.
- Full revision history and a "who published what, when" audit trail (Section 13) — every public-facing statement on a university website is a reputational and sometimes legal artifact.
- Media library with automatic image optimization (responsive sizes, WebP/AVIF generation) on upload — do not make editors manually resize images.
- Scheduled publishing (embargo future news/events until a set date/time).
- A structured "fact" data type for Section 10.3's live statistics, editable by an authorized role, with a visible "last updated" timestamp on the public page — trust is rebuilt by *showing* the data is current, not just claiming it.

---

## 9. UI/UX & Design System Requirements

**As a UI expert's assessment:** the current site has no discernible design system, inconsistent spacing/typography, and a "GU at a Glance" section that visibly undermines trust with placeholder data. The new site must correct this at the systemic level, not page-by-page.

- **Design tokens** (colors, spacing scale, typography scale, radii, shadows) defined once as CSS custom properties and reused everywhere — no inline hex codes in templates.
- **Brand palette** (shared with `gu-services`, Section 3.5): Green `#00BF63` primary, Yellow `#FFF24D` used only for highlight/attention states with dark text (never white-on-yellow — a genuine contrast failure risk), Red `#FF3131` reserved for urgent/alert states (emergency banner, critical notices) so it retains meaning and isn't diluted by decorative overuse.
- **Typography**: a clean, highly legible system font stack (performance-first — avoid heavy webfont loading that hurts Core Web Vitals) with a defined scale (H1–H6, body, caption) and a minimum body size of 16px.
- **Component library**: buttons, cards, nav, breadcrumbs, tabs, accordions, forms, alert/banner, badges (for eServices status), pagination — built once, documented, reused across every page template rather than one-off markup per page.
- **Responsive design**: mobile-first, tested at minimum at 360px, 768px, 1024px, 1440px breakpoints. A large share of GU's prospective-student audience is mobile-first on constrained data plans — this is not a "nice to have" breakpoint exercise, it's the primary access pattern.
- **Imagery**: real, current, consented photography of campus/students/faculty — not generic stock imagery — reinforcing authenticity and the "Community Transformation" identity.
- **Motion**: purposeful, subtle micro-interactions (hover states, page transitions, skeleton loading states) that respect `prefers-reduced-motion` — modern without being gratuitous or a performance/accessibility liability.
- **Dark mode**: originally scoped as a Phase 3+ evaluation once the light-mode design system was solid; **superseded 2026-08-09 by direct client instruction — built site-wide immediately** rather than deferred, once the light-mode design system (by then genuinely mature) made it a low-risk, ~40-line token addition instead of a parallel build. System-preference default (`prefers-color-scheme`) plus an explicit header toggle that always wins and persists via `localStorage`; brand colors stay fixed across both themes, only neutrals and two "deep" text-accent tokens change (contrast-verified programmatically, 7:1–16:1 on every text combination — see `progress.md`).
- **Consistent page templates**: every faculty page, every news article, every event uses the same underlying template with content-driven variation — this is what actually produces a "state of the art, professional" feel at scale, more than any single hero image does.
- **Empty/loading/error states** designed deliberately (e.g. "No news at this time" rather than a broken blank list) — a visible maturity signal.

---

## 10. Data & Analytics Requirements

**As a data analyst's assessment:** the current site's only visible "data" is a broken stats widget. The new site must treat data as a first-class product, not an afterthought.

### 10.1 Web analytics
- Google Analytics 4 (or a privacy-respecting alternative — evaluate against Section 14's data protection posture; a self-hosted option like Matomo is worth considering specifically because it avoids third-party data transfer questions under DPA 2019) implemented site-wide from day one, with consent-gated tracking per Section 14.
- Defined event taxonomy: CTA clicks (Apply Now, eServices links), search queries + zero-result queries, document downloads, outbound portal-link clicks, form submissions/abandonment.
- UTM discipline for all campaign links (recruitment fairs, social media pushes).

### 10.2 KPI dashboard (reviewed monthly by Communications/ICT)
| KPI | Why it matters |
|---|---|
| Admissions-page → `apply.gu.ac.ug` click-through rate | Measures whether the website is actually converting interest into applications |
| Site-wide search zero-result rate | Directly surfaces missing/mislabeled content |
| Top exit pages | Flags weak or confusing pages |
| Core Web Vitals trend | Ties directly to Section 11's performance requirement |
| Accessibility issue count (automated scan, tracked over time) | Ties to Section 15 |
| eServices directory click-through by system | Tells Communications/ICT which systems the public actually uses/needs promoted |
| Downloads by document | Informs which policies/forms actually get used |

### 10.3 Live institutional statistics ("GU at a Glance," fixed)
This directly replaces the current site's broken placeholder widget:
- Number of academic programmes, current student enrolment, undergraduate pass rate, % modules online, total alumni — each backed by a real, dated data source (either the CMS's structured "fact" type from Section 8, or, once available, a live read from `gu-services`'s planned student/academic systems).
- Every stat carries a visible "as of [date]" label — never presented as if perpetually real-time when it's actually a manually-updated figure, and never left silently stale.
- A stat with no verified source is not published as "0" (which reads as broken) — it is simply omitted from the page until a real figure exists.

### 10.4 Search analytics feeding content strategy
Zero-result and low-click-through search queries are reviewed monthly as a direct input to the CMS content backlog (Section 8) — a concrete, low-cost mechanism turning visitor behavior into content decisions.

### 10.5 Reporting cadence
- Monthly summary to Communications Office; quarterly summary to University Management including comparison against the KPIs in 10.2 and the goals in Section 4.

---

## 11. Performance & Technical Non-Functional Requirements

- **Core Web Vitals**: LCP < 2.5s, INP < 200ms, CLS < 0.1 on 4G mobile throttling, for the homepage and top 10 traffic pages, verified via Lighthouse/PageSpeed Insights before every release.
- **Image optimization**: responsive `srcset`, modern formats (WebP/AVIF with fallback), lazy-loading below the fold.
- **Caching**: server-side page/fragment caching for high-traffic, low-change pages (home, faculty pages); browser caching headers set correctly for static assets; a CDN in front of static assets and images (evaluate Cloudflare or similar — also brings basic DDoS/WAF benefit, Section 13).
- **Minimal JavaScript**: no heavy SPA framework for a primarily content-driven site — server-rendered PHP with progressive enhancement keeps this fast and simple to secure, consistent with the "HTML + PHP" stack directive.
- **Uptime target**: 99.5%+ for the public site (higher than an internal admin tool needs, since this is the university's public face and admissions season traffic is business-critical).
- **Browser/device support**: current two major versions of Chrome, Firefox, Safari, Edge; iOS Safari and Android Chrome for mobile.
- **Graceful degradation**: core content (text, contact info, admissions info) must remain accessible even if JavaScript fails to load or a third-party script (analytics, chat widget) is blocked/slow.

---

## 12. SEO Requirements

SEO is the practice of improving the website to increase its visibility and rank higher in unpaid, organic search results — it helps search engines understand GU's content, driving targeted prospective-student/researcher/public traffic and long-term value without paying for ads. This is a standing requirement for every page on the site, not a launch-week add-on, and applies the same standard for any future GU web property built alongside this one.

### 12.1 Core pillars

| Pillar | Requirement |
|---|---|
| **Relevance** | Match content closely to the words and intent of what users are actually searching for (e.g. "Gulu University admission requirements," "Faculty of Medicine Uganda") |
| **Authority** | Build trust through high-quality inbound links and positive brand signals from other reputable sites (government, accreditation bodies, partner universities, press coverage) |
| **Technical Structure** | Ensure search engine bots can easily crawl, read, and index pages quickly and securely |

### 12.2 Four types of optimization to cover

1. **On-Page SEO** — fine-tune text, titles, headings, meta descriptions, and images on every individual page (every CMS content type gets required title/description fields, Section 8).
2. **Off-Page SEO** — earn external links, mentions, and social proof (partner institutions, government/accreditation bodies, press) that elevate the site's credibility — a Communications Office/PR activity, not just an engineering one, but the site must make itself link-worthy (Section 7.9's Press/Media page, shareable content) to support it.
3. **Technical SEO** — site speed (Section 11's Core Web Vitals budget), mobile usability, and clean, crawlable code architecture (semantic HTML5 — proper heading hierarchy, `<nav>`, `<main>`, `<article>` — which also directly supports Section 15's accessibility requirement).
4. **Local SEO** — optimize for geographic/map-based queries relevant to each physical campus (Main/Gulu, Kitgum, Kotido, Hoima, Moroto Constituent College) — a Google Business Profile (or equivalent) per campus location, kept current and linked from the Contact/campus pages.

### 12.3 Requirements checklist (verify before treating any page/section's SEO as done)

- [ ] Content aligns with target keywords and search intent
- [ ] Titles, meta descriptions, and headings optimized on every page (first-class required CMS fields per content type, Section 8 — never left to auto-generation)
- [ ] Images include descriptive alt text (doubles as the Section 15 accessibility requirement) and are compressed (ties to Section 11's image-optimization pipeline)
- [ ] Inbound link strategy in place from reputable sources
- [ ] Site loads fast and passes mobile usability checks (Section 11)
- [ ] Clean, crawlable code architecture with a valid XML sitemap (auto-generated from the CMS, submitted to Google Search Console/Bing Webmaster Tools) and a deliberately-reviewed `robots.txt` (not a WordPress default inherited by habit)
- [ ] HTTPS enabled site-wide (ties to Section 13.2's TLS/HSTS requirement)
- [ ] Local business profiles claimed and optimized for each campus (Section 12.2's Local SEO)

### 12.4 Additional structural requirements

- `schema.org` structured data: `EducationalOrganization`, `Event` (events calendar), `JobPosting` (vacancies), `Article` (news) — meaningfully improves discoverability and rich-result eligibility.
- Clean, human-readable URLs (`/academics/faculty-of-medicine`, not `?page_id=482`).
- Canonical URLs and correct handling of legacy WordPress URL redirects (Section 17) to preserve existing search rankings through the migration.

---

## 13. Cybersecurity Requirements

**As a cybersecurity expert's assessment:** a public university website is a high-value target (defacement, data theft from forms, malware injection, reputational attack) and this migration is the right moment to leave WordPress's plugin/theme CVE exposure behind entirely — provided the replacement is built to an equivalent-or-better standard, not just "different, therefore safer." The standard set here **mirrors and extends `gu-services`'s own hardened rules** (see its `CLAUDE.md`), since both projects should meet the same bar.

### 13.1 Application-layer security (non-negotiable, every form, every page)
- **SQL Injection**: PDO prepared statements with bound parameters, everywhere, via a single shared `db()` connection helper — never string-concatenated SQL, matching `gu-services`'s own Rule 4.
- **XSS**: escape all dynamic output with a shared `esc()` helper; never `echo` raw user or CMS input without escaping, including CMS-authored rich text (sanitize on save, escape on output — belt and suspenders).
- **CSRF**: every state-changing form (contact, newsletter signup, CMS admin actions) includes a CSRF token, verified server-side before any write.
- **File uploads**: strict extension whitelist + real server-side MIME re-validation (`finfo`, never trust client-reported type) for CMS media uploads and any CV/document uploads (Careers page, if a submission form is ever added) — matching `gu-services`'s upload validation standard.
- **Rate limiting**: every form submission endpoint and the search endpoint (protects against scraping/DoS via expensive queries) — DB-backed, logged on violation, matching `gu-services`'s `ratelimit.php` pattern.
- **Session & auth (CMS admin only)**: bcrypt/argon2 password hashing, secure/httponly/samesite session cookies, session regeneration on login, enforced inactivity timeout, and **mandatory 2FA for CMS Content Approver and Administrator roles** — an addition beyond `gu-services`'s current baseline, justified because a compromised public-website admin account is a direct defacement/reputational risk with immediate public visibility, unlike an internal HR record.
- **Admin area hardening**: CMS admin login path not indexed/discoverable via a predictable slug pattern where feasible, rate-limited, and monitored for repeated failed attempts with alerting.

### 13.2 Infrastructure & transport
- TLS everywhere (HSTS enabled), no mixed content.
- Security headers: `Content-Security-Policy`, `X-Content-Type-Options`, `X-Frame-Options`/`frame-ancestors`, `Referrer-Policy`, `Permissions-Policy` — set deliberately, tested against the actual assets/scripts the site loads (a CSP that's copy-pasted without testing is worse than none).
- A CDN/WAF layer (Section 11) providing basic bot/DDoS mitigation for a public, unauthenticated site that will see unpredictable traffic spikes (admissions deadlines, viral news).
- Regular, automated backups of both the database and uploaded media, tested restores on a defined schedule — not just backups that have never been proven to restore.

### 13.3 Process & governance
- A published **`security.txt`** and a real vulnerability disclosure contact/process — a modern, low-cost trust signal almost no Ugandan university site currently has.
- Dependency hygiene: since the stack is plain PHP with no framework (Section 16), this mainly means keeping PHP itself and the web server patched — if any third-party PHP library is ever added, it goes through explicit review and stays patched (mirrors `gu-services`'s "no Composer dependencies by design" stance).
- Centralized, hash-chained **audit logging** for every CMS admin action (publish, delete, permission change) — reusing the same append-only audit-log pattern `gu-services` already implements, so incident investigation on either project follows the same playbook.
- Error handling: no stack traces, DB errors, or file paths ever exposed to the public; `APP_ENV`-gated verbose logging to a private log file only, matching `gu-services`'s Rule 4.
- A basic incident response plan: who is notified, what gets taken offline first, how a breach affecting personal data triggers the Section 14 breach-notification process.
- Periodic (at minimum pre-launch, then annual) penetration testing / security review, scoped to both the CMS admin surface and every public form.

### 13.4 The cross-project boundary is itself a security boundary
The integration bridge in Section 3.3 is the single most sensitive piece of new infrastructure this project introduces, because it is the one place gu-website code touches `gu-services`'s data. It must be: least-privilege (read-only, narrow table/column whitelist), never exposed as a public-facing endpoint without its own authentication if implemented as an HTTP API, and explicitly covered in the pre-launch security review — a vulnerability here is a vulnerability in *both* systems at once.

---

## 14. Data Protection & Privacy Requirements

**As a data protection expert's assessment:** the current site's data handling posture is unknown/unverified — no visible privacy policy or cookie consent was identified in the review. Uganda's **Data Protection and Privacy Act, 2019 (DPPA 2019)**, enforced by the **Personal Data Protection Office (PDPO)**, applies directly since Gulu University is processing personal data of Ugandan (and international) data subjects. The new site must treat this as a hard legal requirement, not a generic "add a cookie banner" checkbox.

### 14.1 Data inventory (build this before writing any form)
Every page/feature that collects or displays personal data must be catalogued: what data, why (lawful basis), who can access it, how long it's retained, and whether it's shared with `gu-services` or any third party (e.g. an analytics provider, an email newsletter service). Candidate data flows on this site:

| Data flow | Personal data involved | Notes |
|---|---|---|
| Contact/inquiry form | Name, email, phone, message | Define retention and which office actually receives it |
| Newsletter signup | Email, optionally name | Requires explicit opt-in, not pre-checked; easy one-click unsubscribe |
| "Report a Concern" channel | Potentially sensitive (safeguarding, grievance details), possibly special-category data | Highest sensitivity on the site — needs restricted access, encryption at rest, and a defined handling SOP, not just a generic contact form |
| Careers/vacancy interest (if any submission point exists) | CV contents (highly personal — education history, sometimes national ID, photo) | If the website ever collects a CV directly rather than only linking to the HR system, treat this with the same rigor as `gu-services`'s confidential-file tier |
| Site analytics | IP address, device/browser fingerprint, behavior | Requires consent banner logic (Section 14.3); prefer IP anonymization |
| Public staff directory (Section 7.13) | Name, official role, official contact details | Only publish fields staff have consented to make public/that are already institutionally public — never pull directly from HR's confidential schema |

### 14.2 Core principles applied concretely
- **Lawful basis per data flow** — legitimate interest for basic inquiry handling, consent for newsletter/marketing and non-essential cookies, explicit safeguarding-appropriate handling for the concern-reporting channel.
- **Data minimization** — every form field must be justified; do not collect national ID numbers, dates of birth, or other high-sensitivity fields on the public website unless a specific, justified process requires it (and if so, treat it with Section 13's confidential-file-grade handling, not a plain contact-form table).
- **Purpose limitation** — data collected for one purpose (e.g. newsletter signup) is not silently reused for another (e.g. admissions marketing) without fresh consent.
- **Storage limitation / retention schedule** — a defined, documented retention period per data flow (e.g. general inquiries purged/anonymized after 12 months unless part of an active case), enforced by a scheduled cleanup job, not left to accumulate indefinitely.
- **Integrity & confidentiality** — encryption in transit (Section 13.2) and at rest for any elevated-sensitivity table (the concern-reporting channel especially); access restricted by role, with audit logging on every access to sensitive submissions.
- **Accountability** — a named data protection contact/role at the university for this website (even if it's the same person who'd be a formal Data Protection Officer under DPPA 2019 more broadly), published in the privacy policy.

### 14.3 Consent & transparency
- A clear, layered **cookie/consent banner** (accept/reject/customize — not just an "OK" dismiss button that implies consent regardless of choice) gating non-essential analytics/marketing cookies, with the choice honored technically (no GA4 tag firing before consent is given).
- A genuine, GU-specific **Privacy Policy** page — not a generic template — covering: what's collected, why, lawful basis, retention, third parties/processors (including the `gu-services` data bridge, disclosed as an internal data-sharing arrangement), data subject rights, and the contact for exercising them.
- **Data subject rights** (access, correction, deletion/restriction where applicable under DPPA 2019) supported by a real, working process — even if manual/email-based at launch — not just asserted in the policy text with no actual mechanism behind it.

### 14.4 Special considerations for this institution
- **Prospective students may include minors** (secondary-school leavers, some under 18 depending on programme/context) — admissions-related data collection and any age-relevant consent handling should account for this rather than assuming every visitor is a legal adult.
- **The concern/safeguarding channel** (Section 7.13) may receive special-category or highly sensitive disclosures — this single feature should be scoped and reviewed with disproportionate care relative to its apparent simplicity; do not treat it as "just another contact form."
- **Cross-border data transfer**: if any third-party analytics/email/hosting provider stores data outside Uganda, this must be disclosed in the privacy policy and evaluated for DPPA 2019 cross-border transfer compliance (adequate safeguards) — a self-hosted analytics option (Section 10.1) sidesteps this entirely and is worth weighing for that reason alone.
- **Breach notification**: a defined internal process for detecting, assessing, and — where the legal threshold is met — notifying the PDPO and affected data subjects, aligned with DPPA 2019's obligations, feeding directly from Section 13.3's incident response plan.

---

## 15. Accessibility Requirements

- **WCAG 2.2 Level AA** as the explicit, non-negotiable conformance target across every page — not "best effort."
- Semantic HTML, correct heading hierarchy, ARIA landmarks, visible focus states, full keyboard operability (no mouse-only interactions), skip-to-content links.
- Color contrast verified programmatically for every design-token combination (Section 9) before it's used in a component — especially the yellow accent, which fails contrast with light/white text and must only pair with dark text.
- Alt text required as a mandatory CMS field for every image (Section 8) — not optional, not silently left blank.
- Captions/transcripts for any video content (VC messages, promotional videos, virtual tour).
- `prefers-reduced-motion` respected for all animations/transitions (Section 9).
- A published **Accessibility Statement** page naming the conformance target, known limitations if any, and a real contact for reporting barriers — itself a WCAG-adjacent best practice and a trust signal.
- Testing regime: automated scanning (axe-core/Lighthouse) in the build/release process, plus periodic manual testing with a screen reader (NVDA/VoiceOver) and keyboard-only navigation — automated tools alone catch roughly a third of real issues.
- This is not a generic legal-compliance checkbox for GU specifically: the university's own mission (community transformation, rural inclusion) is directly undermined by a site that excludes users with disabilities — treat this section as mission-aligned, not just risk-mitigation.

---

## 16. Technical Architecture & Stack

Per the explicit brief ("using html and php") and for direct consistency/maintainability alongside `gu-services`:

- **Backend**: Plain PHP (current stable 8.x), no framework — same architectural choice `gu-services` already made and documented its reasoning for (simplicity, no framework upgrade treadmill, full control, one skillset across both projects).
- **Database**: MySQL/MariaDB, InnoDB, `utf8mb4` — matching `gu-services`.
- **Structure** (lightweight MVC-by-convention, not a formal framework):
  ```
  gu-website/
  ├── config/            (bootstrap, db() connection helper, environment config)
  ├── includes/          (security.php [esc(), CSRF], validation.php [Validator],
  │                       ratelimit.php, audit.php, layout partials, nav,
  │                       gu_services_bridge.php [Section 3.3])
  ├── public/ (or web root)
  │   ├── assets/        (css, js, images — with cache-busting via ?v=filemtime())
  │   └── *.php           (public-facing pages, one per route/section)
  ├── admin/             (CMS — auth-gated, its own small role model, Section 8)
  ├── database/          (schema_*.sql files, one per logical area, checked into
  │                       source control — no migration tool, matching gu-services'
  │                       "no migration tool" convention for consistency)
  └── storage/           (uploaded media, private logs — outside public webroot
                          where sensitive, e.g. concern-report attachments)
  ```
- **Front-end**: Server-rendered HTML/PHP templates, a hand-built (not vendor-locked) CSS design system (Section 9) using CSS custom properties for tokens, minimal vanilla JS for progressive enhancement (nav toggle, form validation UX, carousel/tabs where genuinely needed) — no heavy SPA framework, matching Section 11's performance rationale.
- **Environment management**: `.env`-based config (mirroring `gu-services`), never committing secrets; `APP_ENV` gating error verbosity (Section 13.3). Include `ESERVICES_URL` (Section 3.6) — `http://localhost/gu-services` in dev, `https://eservices.gu.ac.ug` in production — as a required config value from Phase 1 onward.
- **Version control**: Git from day one (the current project has none) — this alone is a prerequisite for safe iterative development and is listed as a Phase 0 action item (Section 21).
- **Hosting**: confirm target (the current live site is presumably hosted separately from this XAMPP dev environment) — production TLS/CDN/WAF per Section 13.2, staging environment mirroring production for pre-release testing (Section 18).

---

## 17. Content Migration Plan

- **Content audit**: inventory every page/post/document on the current WordPress site; classify as migrate-as-is, migrate-and-rewrite, or retire.
- **Data correction pass**: resolve the placeholder "GU at a Glance" stats (Section 10.3) and the duplicate-events bug (Section 7.6) as part of migration, not after — do not carry known-broken data into the new system.
- **URL mapping & redirects**: a full old-URL → new-URL redirect map (301s) to preserve existing search rankings and any external links (e.g. from accreditation bodies, partner sites) that point at the current site.
- **Media migration**: re-optimize all migrated images through the new pipeline (Section 11) rather than copying WordPress's originals as-is.
- **Verification pass**: every migrated fact (faculty names, contact details, governance names/titles) re-verified against the Communications Office before go-live, not trusted as accurate just because it existed on the old site.

---

## 18. Testing & QA Requirements

- Cross-browser/device testing matrix per Section 11.
- Automated accessibility scanning in CI/pre-release (Section 15), plus a manual pass before each major release.
- Security testing: the checklist in Section 13 run through pre-launch, and a real penetration test/security review before go-live and annually thereafter.
- Performance testing: Lighthouse/PageSpeed run on every release candidate against the Section 11 budgets.
- Content QA: a defined editorial review step (Content Approver role, Section 8) before anything public-facing is published — direct mitigation for the current site's duplicate-events and stale-stats problems.
- User acceptance testing with real representatives of at least three personas (Section 5) — e.g. a Communications Office staffer, a current student, and someone testing with a screen reader — before launch.
- A staging environment (Section 16) used for every pre-release check, never testing directly against production.

---

## 19. Launch, Maintenance & Governance

- **Editorial ownership**: Communications Office holds Content Editor/Approver roles (Section 8); ICT/engineering owns the codebase, security posture, and the `gu-services` integration bridge.
- **Change management for the integration bridge** (Section 3.3): any schema change on the `gu-services` side that affects the read whitelist must be coordinated — document this dependency explicitly in both projects' `progress.md`/`CLAUDE.md` files so a future session on either project doesn't break the other silently.
- **SLA targets**: align with Section 11's uptime target; define an incident severity/response matrix (tied to Section 13.3's incident response plan).
- **Post-launch cadence**: monthly analytics/KPI review (Section 10.5), quarterly accessibility re-scan, annual security review, ongoing content freshness audits (flagging pages untouched for 12+ months for review).

---

## 20. Risks & Mitigations

| Risk | Mitigation |
|---|---|
| `gu-services` schema changes silently break the integration bridge | Explicit shared dependency documentation (Section 19); bridge code isolated to one file/module (Section 3.3) so breakage is fast to locate and fix |
| Scope creep — website starts reimplementing transactional features that belong in `gu-services` | Section 3.2's explicit boundary, revisited at every phase gate (Section 21) |
| Placeholder/incorrect content shipped again (repeat of the "0%" problem) | Section 8's "no fact published without a verified source" rule; Content Approver sign-off workflow |
| Public form (concern-reporting especially) mishandled as a generic contact form | Section 14.4's explicit elevated-care flag; a dedicated data-handling SOP before this feature ships |
| Accessibility treated as a launch-week retrofit rather than built in | Section 15 embedded in the design-system and CMS requirements from Section 9/Section 8 onward, not appended late |
| Security regressions from custom PHP replacing a (patched) WordPress install | Section 13's mirrored-and-extended `gu-services` standard, plus mandatory pre-launch penetration test |
| Migration loses SEO ranking | Section 17's explicit URL-redirect mapping requirement |

---

## 21. Roadmap / Phased Delivery (recommended)

| Phase | Focus |
|---|---|
| 0 | Git initialized; environment/config scaffolding; confirm hosting/domain plan; confirm integration mechanism choice (Section 3.3, DB-link vs internal API) with client |
| 1 | Design system (Section 9) + core page templates + IA/sitemap (Section 6) finalized; CMS core (Section 8) for News/Events/Pages; security baseline (Section 13.1) built in from the first line of code, not bolted on later |
| 2 | Full content build-out (Academics, Admissions, Research, Community Engagement); `gu-services` integration bridge live (eServices directory, academic calendar); content migration (Section 17) |
| 3 | Careers/vacancy read-only integration; Press/Media section; Downloads/GU-DMS integration; analytics/KPI dashboard live (Section 10); accessibility and security pre-launch audits (Sections 15/13) |
| 4 | Launch; post-launch monitoring cadence begins (Section 19); evaluate Luo/Acholi content layer (Section 7.12); ~~evaluate dark mode~~ — built site-wide 2026-08-09, ahead of schedule, per direct client instruction (Section 9) |
| 5+ | AI-assisted admissions FAQ (Section 7.11); full SSO exploration with `gu-services` (Section 3.4); deeper research-repository/graduate-tracking integrations as those `gu-services` systems come online |

Do not begin Phase 2 content build-out before Phase 1's design system and security baseline are in place — building content pages against an undefined design system is exactly how the current site ended up visually inconsistent.

---

## 22. Open Questions / Decisions Needed (confirm with client/Communications before or during Phase 0–1)

1. ~~eServices entry-point URL~~ — **resolved 2026-08-09** (Section 3.6): single config-driven link, `localhost/gu-services` in dev / `eservices.gu.ac.ug` in production. The deeper question of the live per-system directory's integration mechanism (shared-DB read access vs. internal HTTP API bridge to `gu-services`, Section 3.3) remains open and is deferred to Phase 2+ — pick one before that phase starts, not before.
2. Hosting target for production (current dev is local XAMPP) — determines TLS/CDN/WAF setup specifics (Section 13.2).
3. Analytics platform choice: GA4 vs. a self-hosted alternative (Section 10.1/14.4) — has real data-protection implications, not just a tooling preference.
4. Scope and process owner for the "Report a Concern" channel (Section 7.13/14.4) — who actually receives and handles these, and under what existing university policy.
5. Appetite/timeline for a Luo/Acholi content layer (Section 7.12).
6. Whether any CV/document upload will ever happen directly on the public website (Careers section) or remain strictly link-out to the HR system (Section 3.2) — materially changes Section 13/14 scope if the former.
7. Named institutional source(s) of truth for the Section 10.3 statistics, and update cadence.
8. Confirm current brand asset files (logo, crest, official photography) are available and match what's already used in `gu-services` (Section 3.5).

---

*This document should be read alongside `CLAUDE.md` (engineering rules, binding every session) and `progress.md` (live project state). It is a living requirements spec — update it deliberately, with a dated note, if a client decision changes something written here; do not let `progress.md` silently drift out of sync with what this document says is required.*
