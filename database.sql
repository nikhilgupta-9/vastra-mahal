-- Vastra Mahal Database Schema

CREATE DATABASE IF NOT EXISTS `vastra_mahal_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vastra_mahal_db`;

-- 1. Admin Users Table
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `image` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `is_featured` TINYINT(1) DEFAULT 1,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `sku` VARCHAR(50) NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `sale_price` DECIMAL(10,2) NULL,
  `fabric` VARCHAR(100) NULL,
  `color` VARCHAR(100) NULL,
  `work_type` VARCHAR(150) NULL,
  `short_desc` TEXT NULL,
  `description` LONGTEXT NULL,
  `main_image` VARCHAR(255) NOT NULL,
  `gallery_images` TEXT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_new_arrival` TINYINT(1) DEFAULT 1,
  `in_stock` TINYINT(1) DEFAULT 1,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Contact Inquiries Table
CREATE TABLE IF NOT EXISTS `contact_inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `subject` VARCHAR(200) NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Default Admin: username: admin / password: admin123
INSERT INTO `admin_users` (`username`, `email`, `password`, `name`, `role`)
VALUES ('admin', 'admin@vastramahal.com', '$2y$10$sDcyaGOKuw3Zx1zqP.p.uOOggU94.Yuax/8zWvYq/OEtlp7dfDLqC', 'Store Admin', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Seed Site Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('store_name', 'Vastra Mahal'),
('store_tagline', 'Royal Heritage Ethnic Couture & Handloom Silks'),
('phone_number', '+91 98765 43210'),
('whatsapp_number', '+919876543210'),
('store_email', 'contact@vastramahal.com'),
('store_address', 'Shop No. 12, Heritage Fashion Arcade, Janakpuri / Uttam Nagar, New Delhi - 110059'),
('google_map_url', 'https://maps.app.goo.gl/73nDqtqFGqEFEmUf6'),
('google_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.825856456073!2d77.0983074!3d28.6053069!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjjCsDM2JzE5LjEiTiA3N8KwMDYnMDMuMiJF!5e0!3m2!1sen!2sin!4v1700000000000'),
('store_timings', 'Mon - Sun: 10:30 AM to 9:00 PM'),
('instagram_url', 'https://instagram.com/vastramahal_official'),
('facebook_url', 'https://facebook.com/vastramahal')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);
