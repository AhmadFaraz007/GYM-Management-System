-- Create the database
CREATE DATABASE IF NOT EXISTS flexfusion_db;
USE flexfusion_db;

-- Users: Admins, Trainers, Members
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'trainer', 'member') NOT NULL,
    phone VARCHAR(20),
    gender ENUM('male', 'female', 'other'),
    date_of_birth DATE,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Member-Trainer Assignments
CREATE TABLE members_trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    trainer_id INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Workout Plans
CREATE TABLE workout_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Diet Plans
CREATE TABLE diet_plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    created_by INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Plan Assignments to Members
CREATE TABLE member_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    workout_plan_id INT,
    diet_plan_id INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (workout_plan_id) REFERENCES workout_plans(plan_id) ON DELETE SET NULL,
    FOREIGN KEY (diet_plan_id) REFERENCES diet_plans(plan_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Progress Records
CREATE TABLE progress_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    recorded_by INT,
    weight DECIMAL(5,2),
    calories_burned INT,
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Attendance Tracking
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    checkin_date DATE,
    status ENUM('present', 'absent') DEFAULT 'present',
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Subscription Records
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    package_name VARCHAR(100),
    amount_paid DECIMAL(10,2),
    start_date DATE,
    end_date DATE,
    payment_status ENUM('paid', 'pending') DEFAULT 'paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Chat Messages
CREATE TABLE chat_messages (
    msg_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admin Announcements
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    message TEXT,
    posted_by INT,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Feedback from Members
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;
