-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS `college_complaint_db`;
USE `college_complaint_db`;

-- 1. Users Table (Students and Faculty)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `contact_no` VARCHAR(15) NOT NULL,
  `roll_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_type` ENUM('student', 'faculty') DEFAULT 'student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Complaints Table
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `attachment` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Pending', 'In Progress', 'Closed') DEFAULT 'Pending',
  `remark` TEXT DEFAULT NULL,
  `remark_date` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin (username: admin, password: admin123)
INSERT INTO `admins` (`id`, `username`, `password`, `fullname`, `email`) 
VALUES (1, 'admin', '$2y$10$zrJ2WI0d8JIVnd7DYGWsI.JMqRSmb7oOVLiwwEwElq84rA/ytXF1W', 'System Administrator', 'admin@college.edu')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Insert Initial Categories
INSERT INTO `categories` (`name`, `description`) VALUES
('Academic', 'Issues related to lectures, exams, syllabus, or professors'),
('Hostel & Mess', 'Problems regarding hostel rooms, cleanliness, food quality, or boarding'),
('Infrastructure & Amenities', 'Complaints on library, labs, internet/Wi-Fi, classroom equipment, or sports facilities'),
('Finance & Fees', 'Issues relating to tuition fees, scholarships, or fine disputes'),
('General & Anti-Ragging', 'Other grievances and safety-related complaints')
ON DUPLICATE KEY UPDATE `name`=`name`;
