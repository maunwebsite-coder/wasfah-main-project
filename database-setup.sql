-- Database setup script for Wasfah Backend
-- Run this script after creating the MySQL user

-- Create database
CREATE DATABASE IF NOT EXISTS wasfah_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER IF NOT EXISTS 'wasfah_user'@'localhost' IDENTIFIED BY 'your_secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON wasfah_backend.* TO 'wasfah_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Show databases to confirm
SHOW DATABASES;

-- Show users to confirm
SELECT User, Host FROM mysql.user WHERE User = 'wasfah_user';
