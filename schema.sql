-- Create the database
CREATE DATABASE IF NOT EXISTS it_issues;

-- Use the database
USE it_issues;

-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Create the departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL
);

-- Create the issue_categories table
CREATE TABLE IF NOT EXISTS issue_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL
);

-- Create the devices table
CREATE TABLE IF NOT EXISTS devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    device_type VARCHAR(255) NOT NULL,
    serial_number VARCHAR(255) UNIQUE NOT NULL
);

-- Create the issues table
CREATE TABLE IF NOT EXISTS issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    department_id INT,
    category_id INT,
    device_id INT,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (category_id) REFERENCES issue_categories(id),
    FOREIGN KEY (device_id) REFERENCES devices(id)
);
