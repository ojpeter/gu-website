<?php

/**
 * Primary navigation data — single source of truth for the header's
 * mega-menu. Matches the university's real, full navigation depth (as
 * audited from the live gu.ac.ug site, requirements.md Section 2/6) rather
 * than a simplified version. Kept as plain data so a future CMS "manage
 * navigation" screen can replace this array with a DB read without
 * touching layout_header.php.
 *
 * Shape: each top-level item has a 'label' and either:
 *   - 'url' (a plain link, no dropdown), or
 *   - 'groups' => ['Group Label' => [['label','url'], ...], ...] (a
 *     dropdown mega-panel, one column per group).
 *
 * Leaf items point at a real anchor/page where one already has genuine
 * content (About/Admissions/Academics/Research/Student Life/Services); for
 * everything else they point at page?slug=... — a single generic,
 * registry-driven content page (see page.php) rather than dozens of
 * one-off files, so every menu item is a real, working link with an
 * honest "content pending" state instead of a 404 or fabricated content.
 */
function primary_nav_items(): array
{
    return [
        ['label' => 'Home', 'url' => 'index', 'icon' => 'home'],
        [
            'label' => 'About Us',
            'url' => 'about',
            'icon' => 'about',
            'groups' => [
                'Get Informed' => [
                    ['label' => 'Our History', 'url' => 'page?slug=our-history'],
                    ['label' => 'Vision and Mission', 'url' => 'about#vision-mission'],
                    ['label' => 'Our Policies', 'url' => 'page?slug=our-policies'],
                    ['label' => 'Strategic Plans', 'url' => 'page?slug=strategic-plans'],
                    ['label' => 'Anthem', 'url' => 'page?slug=anthem'],
                    ['label' => 'Administrative Offices', 'url' => 'services'],
                    ['label' => 'Organogram', 'url' => 'page?slug=organogram'],
                ],
                'Governance' => [
                    ['label' => 'The Chancellor', 'url' => 'about#chancellor'],
                    ['label' => 'The Vice Chancellor', 'url' => 'about#vice-chancellor'],
                    ['label' => 'University Council', 'url' => 'about#council'],
                    ['label' => 'University Management', 'url' => 'about#management'],
                    ['label' => 'University Senate', 'url' => 'about#senate'],
                    ['label' => 'Staff Directory', 'url' => 'staff-directory'],
                ],
            ],
        ],
        [
            'label' => 'Study',
            'url' => 'admissions',
            'icon' => 'study',
            'groups' => [
                'Admissions' => [
                    ['label' => 'Apply Online', 'url' => 'https://apply.gu.ac.ug', 'external' => true],
                    ['label' => 'Application Guidelines', 'url' => 'page?slug=application-guidelines'],
                    ['label' => 'Call for Applications', 'url' => 'page?slug=call-for-applications'],
                    ['label' => 'Postgraduate Programmes', 'url' => 'admissions#postgraduate'],
                    ['label' => 'Undergraduate Programmes', 'url' => 'admissions#undergraduate'],
                    ['label' => 'Fees Structure', 'url' => 'admissions#fees-scholarships'],
                    ['label' => 'Admission Lists', 'url' => 'page?slug=admission-lists'],
                    ['label' => 'Circulars', 'url' => 'page?slug=circulars'],
                ],
                'Academic Records' => [
                    ['label' => 'Courses & Programs', 'url' => 'academics'],
                    ['label' => 'Timetables', 'url' => 'page?slug=timetables'],
                    ['label' => 'Academic Calendar', 'url' => 'academics#calendar'],
                    ['label' => 'Registration', 'url' => 'page?slug=registration'],
                    ['label' => 'All Upcoming Events', 'url' => 'events'],
                    ['label' => 'Examinations', 'url' => 'page?slug=examinations'],
                    ['label' => 'Grading', 'url' => 'page?slug=grading'],
                    ['label' => 'Graduation Requirements', 'url' => 'page?slug=graduation-requirements'],
                    ['label' => 'Graduation Highlights', 'url' => 'page?slug=graduation-highlights'],
                    ['label' => 'Graduation Lists', 'url' => 'page?slug=graduation-lists'],
                ],
                'Student Life' => [
                    ['label' => 'Undergraduate Prospectus', 'url' => 'page?slug=undergraduate-prospectus'],
                    ['label' => 'Graduate Prospectus', 'url' => 'page?slug=graduate-prospectus'],
                    ['label' => 'International Students', 'url' => 'admissions#international'],
                    ['label' => 'Visit Gulu University', 'url' => 'page?slug=visit-gulu-university'],
                    ['label' => 'Life on Campus', 'url' => 'student-life'],
                    ['label' => 'Accommodation & Student Housing', 'url' => 'student-life#accommodation'],
                    ['label' => 'Students Guild', 'url' => 'student-life#guild'],
                    ['label' => 'Students Portal', 'url' => 'https://myportal.gu.ac.ug', 'external' => true],
                    ['label' => 'Campus Security', 'url' => 'page?slug=campus-security'],
                    ['label' => 'Catering Services', 'url' => 'page?slug=catering-services'],
                    ['label' => 'Health & Medical Access', 'url' => 'student-life#health'],
                    ['label' => 'Religion & Spirituality', 'url' => 'page?slug=religion-and-spirituality'],
                    ['label' => 'Sports & Recreation', 'url' => 'student-life#sports'],
                    ['label' => 'Counselling & Guidance', 'url' => 'page?slug=counselling-and-guidance'],
                    ['label' => 'Scholarships', 'url' => 'admissions#fees-scholarships'],
                ],
            ],
        ],
        [
            'label' => 'Academic Units',
            'url' => 'academics',
            'icon' => 'units',
            'groups' => [
                'Faculties' => [
                    ['label' => 'Faculty of Agriculture and Environment', 'url' => 'academics#agriculture-and-environment'],
                    ['label' => 'Faculty of Business and Development Studies', 'url' => 'academics#business-and-development-studies'],
                    ['label' => 'Faculty of Education and Humanities', 'url' => 'academics#education-and-humanities'],
                    ['label' => 'Faculty of Law', 'url' => 'academics#law'],
                    ['label' => 'Faculty of Medicine', 'url' => 'academics#medicine'],
                    ['label' => 'Faculty of Science', 'url' => 'academics#science'],
                ],
                'Institutes' => [
                    ['label' => 'Institute of Peace and Strategic Studies', 'url' => 'academics#peace-and-strategic-studies'],
                    ['label' => 'Directorate of Research and Graduate Training', 'url' => 'academics#research-and-graduate-training'],
                ],
            ],
        ],
        [
            'label' => 'Research',
            'url' => 'research',
            'icon' => 'research',
            'groups' => [
                'Research' => [
                    ['label' => 'Research Publications', 'url' => 'research#publications'],
                    ['label' => 'Research Projects', 'url' => 'research#projects'],
                    ['label' => 'Research Ethics', 'url' => 'research#ethics'],
                    ['label' => 'Research Partnerships', 'url' => 'research#partnerships'],
                    ['label' => 'Digital Repository', 'url' => 'research#repository'],
                ],
            ],
        ],
        [
            'label' => 'Services',
            'url' => 'services',
            'icon' => 'services',
            'groups' => [
                'University Services' => [
                    ['label' => 'Communications Office', 'url' => 'services#communications-office'],
                    ['label' => 'University Library', 'url' => 'services#university-library'],
                    ['label' => 'Multifunctional Research Laboratories', 'url' => 'services#research-laboratories'],
                    ['label' => 'Computing Services', 'url' => 'services#computing-services'],
                    ['label' => 'Finance Department', 'url' => 'services#finance-department'],
                    ['label' => 'University Hospital', 'url' => 'services#university-hospital'],
                    ['label' => 'Quality Assurance', 'url' => 'services#quality-assurance'],
                    ['label' => 'Estates and Works', 'url' => 'services#estates-and-works'],
                ],
                'Key Units & Offices' => [
                    ['label' => 'University Secretary', 'url' => 'services#university-secretary'],
                    ['label' => 'Academic Registrar', 'url' => 'services#academic-registrar'],
                    ['label' => 'Internal Audit', 'url' => 'services#internal-audit'],
                    ['label' => 'Planning and Development', 'url' => 'services#planning-and-development'],
                    ['label' => 'Dean of Students', 'url' => 'services#dean-of-students'],
                    ['label' => 'Human Resources', 'url' => 'services#human-resources'],
                    ['label' => 'Research Ethics Committee', 'url' => 'services#research-ethics-committee'],
                    ['label' => 'Procurement and Disposal Unit (PDU)', 'url' => 'services#pdu'],
                ],
            ],
        ],
        ['label' => 'Community Engagement', 'url' => 'community-engagement', 'icon' => 'community'],
        ['label' => 'Donate', 'url' => 'donate', 'icon' => 'donate'],
    ];
}

/**
 * Inline SVG icons for the top-level nav tabs. Hand-drawn (not pulled from
 * a third-party icon font/library — keeps Rule 5's "no dependency added
 * casually" and avoids a render-blocking icon-font request), 20x20,
 * stroke-based so they inherit nav-link text color via currentColor. Keyed
 * by the 'icon' value on each primary_nav_items() entry; returns '' for an
 * unknown/missing key so a nav item without an icon degrades silently
 * rather than breaking the layout. $size lets callers other than the nav
 * itself (e.g. the homepage slider's illustration panels) reuse the same
 * hand-drawn shapes at a larger scale instead of duplicating paths.
 */
function nav_icon(string $key, int $size = 18): string
{
    $common = 'width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="nav-icon"';

    $icons = [
        'home' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9a1 1 0 0 0 1 1H9a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1v-9"/>',
        'about' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v6"/><circle cx="12" cy="7.5" r="0.9" fill="currentColor" stroke="none"/>',
        'study' => '<path d="M2 8.5 12 4l10 4.5-10 4.5-10-4.5Z"/><path d="M6 10.8v4.7c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4.7"/><path d="M21 8.5v6"/>',
        'units' => '<path d="M4 21V9l8-5 8 5v12"/><path d="M4 21h16"/><path d="M9 21v-6h6v6"/><path d="M9 9h.01M12 9h.01M15 9h.01M9 12.5h.01M12 12.5h.01M15 12.5h.01"/>',
        'research' => '<path d="M9.5 3h5"/><path d="M10.5 3v5.5L5.8 17a2 2 0 0 0 1.8 3h8.8a2 2 0 0 0 1.8-3l-4.7-8.5V3"/><path d="M8 15h8"/>',
        'services' => '<rect x="3" y="7.5" width="18" height="12" rx="2"/><path d="M8 7.5V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1.5"/><path d="M3 12.5h18"/>',
        'community' => '<circle cx="8.5" cy="8" r="2.8"/><circle cx="16" cy="9" r="2.3"/><path d="M2.8 19c.4-3 2.7-5 5.7-5s5.3 2 5.7 5"/><path d="M13.6 14.3c2.4.2 4.2 2 4.6 4.7"/>',
        'donate' => '<path d="M12 20.5S3.5 15.4 3.5 9.6A4.6 4.6 0 0 1 12 6.8a4.6 4.6 0 0 1 8.5 2.8c0 5.8-8.5 10.9-8.5 10.9Z"/>',
        'news' => '<rect x="3" y="4.5" width="18" height="15" rx="2"/><path d="M7 8.5h10M7 12h10M7 15.5h6"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'login' => '<circle cx="12" cy="8" r="3.4"/><path d="M5 20c1-4.2 4.4-6.2 7-6.2s6 2 7 6.2"/>',
        'pin' => '<path d="M12 21s7-6.5 7-11.5A7 7 0 0 0 5 9.5C5 14.5 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.3"/>',
    ];

    if (!isset($icons[$key])) {
        return '';
    }

    return '<svg ' . $common . '>' . $icons[$key] . '</svg>';
}

/**
 * Registry for the generic content-stub page (page.php) — only slugs
 * listed here render; anything else 404s. Keeps every mega-menu leaf item
 * a real, working link with an honest "content pending" state rather than
 * fabricated content or a dead 404, for items that don't yet have a real
 * home page of their own.
 */
function generic_page_registry(): array
{
    return [
        'our-history' => ['title' => 'Our History', 'eyebrow' => 'About Us'],
        'our-policies' => ['title' => 'Our Policies', 'eyebrow' => 'About Us'],
        'strategic-plans' => ['title' => 'Strategic Plans', 'eyebrow' => 'About Us'],
        'anthem' => ['title' => 'University Anthem', 'eyebrow' => 'About Us'],
        'organogram' => ['title' => 'Organogram', 'eyebrow' => 'About Us'],
        'application-guidelines' => ['title' => 'Application Guidelines', 'eyebrow' => 'Study'],
        'call-for-applications' => ['title' => 'Call for Applications', 'eyebrow' => 'Study'],
        'admission-lists' => ['title' => 'Admission Lists', 'eyebrow' => 'Study'],
        'circulars' => ['title' => 'Circulars', 'eyebrow' => 'Study'],
        'graduation-lists' => ['title' => 'Graduation Lists', 'eyebrow' => 'Study'],
        'undergraduate-prospectus' => ['title' => 'Undergraduate Prospectus', 'eyebrow' => 'Study'],
        'graduate-prospectus' => ['title' => 'Graduate Prospectus', 'eyebrow' => 'Study'],
        'timetables' => ['title' => 'Timetables', 'eyebrow' => 'Study'],
        'registration' => ['title' => 'Registration', 'eyebrow' => 'Study'],
        'examinations' => ['title' => 'Examinations', 'eyebrow' => 'Study'],
        'grading' => ['title' => 'Grading', 'eyebrow' => 'Study'],
        'graduation-requirements' => ['title' => 'Graduation Requirements', 'eyebrow' => 'Study'],
        'graduation-highlights' => ['title' => 'Graduation Highlights', 'eyebrow' => 'Study'],
        'visit-gulu-university' => ['title' => 'Visit Gulu University', 'eyebrow' => 'Study'],
        'campus-security' => ['title' => 'Campus Security', 'eyebrow' => 'Study'],
        'catering-services' => ['title' => 'Catering Services', 'eyebrow' => 'Study'],
        'religion-and-spirituality' => ['title' => 'Religion & Spirituality', 'eyebrow' => 'Study'],
        'counselling-and-guidance' => ['title' => 'Counselling & Guidance', 'eyebrow' => 'Study'],
    ];
}
