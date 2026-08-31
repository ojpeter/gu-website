-- Contact form submissions (requirements.md Section 7.13 / 14.1 data-flow table)
-- Lawful basis: legitimate interest (responding to a direct inquiry the
-- visitor initiated). Retention: purge/anonymize after 12 months unless
-- part of an active case (requirements.md Section 14.2) — enforce via a
-- scheduled cleanup job once one exists; not yet automated in this schema.
USE gu_website;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    status ENUM('new', 'read', 'closed') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
