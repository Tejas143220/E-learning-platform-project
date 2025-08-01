-- Create Database
CREATE DATABASE IF NOT EXISTS bsc_project;
USE bsc_project;

-- Drop tables if they exist
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS courses;

-- Users Table for Login/Registration
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses Table for Add/Remove Courses
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    is_paid BOOLEAN DEFAULT 0,
    price INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Users (Optional)
INSERT INTO users (username, email, password)
VALUES 
('admin', 'admin@example.com', '$2y$10$examplehashreplaceit');

-- Sample Courses
INSERT INTO courses (title, description, is_paid, price) VALUES
('HTML Basics', 'Learn the basics of HTML.', 0, 0),
('CSS Mastery', 'Master layouts, colors, and animations.', 0, 0),
('JavaScript Advanced', 'Deep dive into JS and DOM.', 1, 499),
('React Bootcamp', 'Learn React.js from scratch.', 1, 899);
