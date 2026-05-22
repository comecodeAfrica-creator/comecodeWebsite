-- ComeCode Database Schema
-- MySQL Database setup for admin dashboard

-- Create Database
CREATE DATABASE IF NOT EXISTS comecode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comecode_db;

-- ============ GALLERY TABLE ============
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    caption TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============ EVENTS TABLE ============
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============ CONTACT_SUBMISSIONS TABLE ============
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    service VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('new', 'replied', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============ ABOUT_CONTENT TABLE ============
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hero_title VARCHAR(255),
    hero_subtitle VARCHAR(255),
    hero_description TEXT,
    mission TEXT,
    vision TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============ SITE_SETTINGS TABLE ============
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) UNIQUE NOT NULL,
    setting_value LONGTEXT,
    setting_type ENUM('string', 'json', 'integer', 'boolean') DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============ INSERT DEFAULT DATA ============

-- Insert default about content
INSERT INTO about_content (hero_title, hero_subtitle, hero_description, mission, vision) VALUES
('We are comecode', 'We are your trusted partner', 'Building digital solutions for the fastest-growing companies.', 'Our mission is to deliver exceptional digital experiences that drive growth and innovation.', 'To be the leading digital solutions provider trusted by businesses worldwide.')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'ComeCode', 'string'),
('site_email', 'info@comecode.com', 'string'),
('site_phone', '+234 XXX XXX XXXX', 'string'),
('site_address', 'Lagos, Nigeria', 'string'),
('site_description', 'Digital solutions for forward-thinking companies', 'string'),
('social_links', '{"facebook":"https://facebook.com","twitter":"https://twitter.com","linkedin":"https://linkedin.com","instagram":"https://instagram.com"}', 'json')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ============ CREATE ADMIN USER (Optional) ============
-- Uncomment and use if you want basic admin authentication
-- CREATE TABLE IF NOT EXISTS admin_users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(100) UNIQUE NOT NULL,
--     password VARCHAR(255) NOT NULL,
--     email VARCHAR(255),
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     last_login TIMESTAMP NULL,
--     INDEX idx_username (username)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
