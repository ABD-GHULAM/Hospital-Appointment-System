-- ============================================================
-- Clinic Appointment Management System - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS clinic_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE clinic_management;

-- ------------------------------------------------------------
-- Users table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'patient') NOT NULL DEFAULT 'patient',
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_email (email)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Doctors table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS doctors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    specialization VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    experience INT UNSIGNED NOT NULL DEFAULT 0,
    qualification VARCHAR(200) NOT NULL,
    available_days VARCHAR(100) NOT NULL COMMENT 'Comma-separated: Mon,Tue,Wed',
    consultation_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_doctors_specialization (specialization)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Patients table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS patients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    gender ENUM('male', 'female', 'other') NOT NULL,
    age INT UNSIGNED NOT NULL,
    blood_group VARCHAR(5) DEFAULT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Appointments table
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT UNSIGNED NOT NULL,
    doctor_id INT UNSIGNED NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    reason TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_appointments_date (appointment_date),
    INDEX idx_appointments_status (status),
    INDEX idx_appointments_doctor (doctor_id),
    INDEX idx_appointments_patient (patient_id)
) ENGINE=InnoDB;

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin account (password: admin123 - run database/setup.php after import)
INSERT INTO users (full_name, email, password, role) VALUES
('System Administrator', 'admin@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Doctor users (password: doctor123 - run database/setup.php after import)
INSERT INTO users (full_name, email, password, role) VALUES
('Dr. Sarah Mitchell', 'sarah.mitchell@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor'),
('Dr. James Wilson', 'james.wilson@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor'),
('Dr. Emily Chen', 'emily.chen@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor'),
('Dr. Michael Brown', 'michael.brown@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor');

-- Patient users (password: patient123 - run database/setup.php after import)
INSERT INTO users (full_name, email, password, role) VALUES
('John Anderson', 'john.anderson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient'),
('Maria Garcia', 'maria.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient'),
('David Lee', 'david.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient'),
('Lisa Thompson', 'lisa.thompson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient'),
('Robert Kim', 'robert.kim@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'patient');

-- Doctors
INSERT INTO doctors (user_id, specialization, phone, experience, qualification, available_days, consultation_fee) VALUES
(2, 'Cardiology', '+62 812-3456-7890', 12, 'MD, FACC - Harvard Medical School', 'Mon,Wed,Fri', 250000.00),
(3, 'Dermatology', '+62 813-4567-8901', 8, 'MD, FAAD - Johns Hopkins University', 'Tue,Thu,Sat', 200000.00),
(4, 'Pediatrics', '+62 814-5678-9012', 15, 'MD, FAAP - Stanford University', 'Mon,Tue,Wed,Thu,Fri', 180000.00),
(5, 'Orthopedics', '+62 815-6789-0123', 10, 'MD, FAAOS - Yale School of Medicine', 'Mon,Wed,Thu,Sat', 300000.00);

-- Patients
INSERT INTO patients (user_id, gender, age, blood_group, phone, address) VALUES
(6, 'male', 35, 'O+', '+62 816-1111-2222', 'Jl. Sudirman No. 45, Jakarta Pusat'),
(7, 'female', 28, 'A+', '+62 817-2222-3333', 'Jl. Gatot Subroto No. 12, Jakarta Selatan'),
(8, 'male', 42, 'B+', '+62 818-3333-4444', 'Jl. Thamrin No. 88, Jakarta Pusat'),
(9, 'female', 31, 'AB+', '+62 819-4444-5555', 'Jl. Kemang Raya No. 23, Jakarta Selatan'),
(10, 'male', 55, 'O-', '+62 820-5555-6666', 'Jl. HR Rasuna Said No. 67, Jakarta Selatan');

-- Appointments
INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, reason, notes) VALUES
(1, 1, CURDATE(), '09:00:00', 'approved', 'Chest pain and shortness of breath', NULL),
(2, 2, CURDATE(), '10:30:00', 'pending', 'Skin rash and itching', NULL),
(3, 3, CURDATE(), '14:00:00', 'approved', 'Child vaccination checkup', NULL),
(4, 4, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'pending', 'Knee pain after exercise', NULL),
(5, 1, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '15:30:00', 'approved', 'Routine heart checkup', NULL),
(1, 3, DATE_ADD(CURDATE(), INTERVAL -3 DAY), '10:00:00', 'completed', 'General health consultation', 'Patient responded well to treatment.'),
(2, 1, DATE_ADD(CURDATE(), INTERVAL -5 DAY), '09:30:00', 'completed', 'Blood pressure monitoring', 'BP normalized. Continue medication.'),
(3, 2, DATE_ADD(CURDATE(), INTERVAL -1 DAY), '16:00:00', 'cancelled', 'Acne treatment follow-up', NULL),
(4, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '13:00:00', 'approved', 'Pediatric wellness visit', NULL),
(5, 4, DATE_ADD(CURDATE(), INTERVAL -7 DAY), '08:30:00', 'completed', 'Back pain assessment', 'Recommended physiotherapy.'),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '11:30:00', 'pending', 'Eczema consultation', NULL),
(2, 4, DATE_ADD(CURDATE(), INTERVAL -2 DAY), '14:30:00', 'rejected', 'Joint stiffness', 'Doctor unavailable on requested date.');
