-- QuickBill 305 Database Initialization
CREATE DATABASE IF NOT EXISTS quickbill_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quickbill_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'officer', 'revenue_officer', 'data_collector') NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT IGNORE INTO users (username, email, password_hash, role, first_name, last_name) VALUES 
('admin', 'admin@quickbill305.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator');

-- Create system_settings table
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT IGNORE INTO system_settings (setting_key, setting_value, description) VALUES
('app_name', 'QuickBill 305', 'Application name'),
('currency', 'GHS', 'Default currency'),
('timezone', 'Africa/Accra', 'Default timezone'),
('billing_year', '2025', 'Current billing year');
