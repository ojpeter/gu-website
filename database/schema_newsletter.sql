-- Newsletter signup (requirements.md Section 14.1 data-flow table).
-- Lawful basis: explicit opt-in consent, not legitimate interest — the
-- checkbox must never be pre-checked, and unsubscribe must be one click.
USE gu_website;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL,
    status ENUM('subscribed', 'unsubscribed') NOT NULL DEFAULT 'subscribed',
    unsubscribe_token CHAR(64) NOT NULL,
    ip_address VARCHAR(45) NULL,
    subscribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME NULL,
    UNIQUE KEY uniq_newsletter_email (email),
    UNIQUE KEY uniq_newsletter_token (unsubscribe_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
