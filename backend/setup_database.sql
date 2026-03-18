
CREATE DATABASE IF NOT EXISTS campussafety;
USE campussafety;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(10),
    user_type ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    complaint_type VARCHAR(50),
    title VARCHAR(255),
    description TEXT,
    location VARCHAR(200),
    attachment_path VARCHAR(255),
    status ENUM('submitted', 'assigned', 'in_progress', 'resolved') DEFAULT 'submitted',
    priority ENUM('low', 'medium', 'high', 'emergency') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(user_id)
);

CREATE TABLE complaint_comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE emergency_alerts (
    alert_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    location VARCHAR(200),
    latitude FLOAT,
    longitude FLOAT,
    status ENUM('active', 'responded', 'resolved') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(user_id)
);

CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    complaint_id INT,
    alert_id INT,
    title VARCHAR(200),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id),
    FOREIGN KEY (alert_id) REFERENCES emergency_alerts(alert_id)
);

INSERT INTO users (roll_number, email, password, full_name, phone, user_type) 
VALUES 
('2024001', 'student@test.com', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'Test Student', '9876543210', 'student'),
('ADMIN001', 'admin@test.com', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'Admin User', '9111111111', 'admin');


CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_roll_number ON users(roll_number);
CREATE INDEX idx_student_id ON complaints(student_id);
CREATE INDEX idx_complaint_status ON complaints(status);
CREATE INDEX idx_notification_user ON notifications(user_id);

