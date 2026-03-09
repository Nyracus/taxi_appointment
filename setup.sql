-- Car Workshop Appointment System
-- Import this in phpMyAdmin: select database "taxi", then Import → choose this file → Go

CREATE DATABASE IF NOT EXISTS taxi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taxi;

-- Drop in correct order (appointments references mechanics)
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS mechanics;

-- Mechanics table (max 4 appointments per mechanic per day)
CREATE TABLE mechanics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    car_license VARCHAR(50) NOT NULL,
    car_engine VARCHAR(50) NOT NULL,
    appointment_date DATE NOT NULL,
    mechanic_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(id) ON DELETE CASCADE,
    INDEX idx_date (appointment_date),
    INDEX idx_mechanic_date (mechanic_id, appointment_date),
    INDEX idx_phone_date (phone, appointment_date)
);

-- Insert the 5 mechanics (no hardcoding in PHP; all data from this SQL)
INSERT INTO mechanics (name) VALUES
('Ahmed Hassan'),
('Karim Rahman'),
('Fatima Khan'),
('Omar Ali'),
('Sara Mahmud');
