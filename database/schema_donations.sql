-- Giving/donation inquiries (requirements.md Section 7's "Give / Support GU"
-- section, and Section 14.1's data-flow discipline — same lawful basis as
-- the contact form: legitimate interest in responding to a inquiry the
-- visitor initiated). No payment processing happens on this site — real
-- bank/mobile-money account details must come from the university's
-- Finance Department before being published; this table only captures
-- interest/pledge inquiries for a human follow-up.
USE gu_website;

CREATE TABLE IF NOT EXISTS donation_inquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL,
    phone VARCHAR(45) NULL,
    interest_area VARCHAR(100) NOT NULL,
    message TEXT NULL,
    ip_address VARCHAR(45) NULL,
    status ENUM('new', 'contacted', 'closed') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
