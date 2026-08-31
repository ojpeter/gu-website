-- Gulu University Website — Core schema (Phase 0/1 foundation)
-- No migration tool (CLAUDE.md Rule 3) — apply directly, keep this file in
-- sync with reality. One file per logical area; this one covers the
-- cross-cutting foundation every other schema file builds on.
--
-- 2026-08-09: this file originally also defined `admin_users` and
-- `site_facts` — both dropped the same day once the CMS admin panel moved
-- to gu-services (GU-CMS module) per client instruction. This project has
-- no admin accounts of its own and reads facts read-only from gu-services'
-- `cms_site_facts` via includes/gu_services_bridge.php instead. See
-- progress.md for the full history; not reproduced here.

CREATE DATABASE IF NOT EXISTS gu_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gu_website;

-- DB-backed rate limiter (CLAUDE.md Rule 2) — one row per hit, counted
-- within a sliding window per bucket_key (e.g. 'login:203.0.113.5',
-- 'contact_form:203.0.113.5').
CREATE TABLE IF NOT EXISTS rate_limit_hits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bucket_key VARCHAR(191) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bucket_time (bucket_key, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Append-only, hash-chained audit log (CLAUDE.md Rule 5) — every admin
-- publish/edit/delete/permission-change, plus throttle violations.
CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_type VARCHAR(20) NOT NULL DEFAULT 'system' COMMENT 'admin | system | anonymous',
    actor_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NULL,
    entity_id VARCHAR(100) NULL,
    ip_address VARCHAR(45) NULL,
    details TEXT NULL,
    prev_hash CHAR(64) NULL,
    hash CHAR(64) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action_time (action, created_at),
    INDEX idx_actor (actor_type, actor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
