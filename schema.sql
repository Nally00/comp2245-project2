-- Create database
CREATE DATABASE IF NOT EXISTS bugme;
USE bugme;

-- Users table
CREATE TABLE IF NOT EXISTS Users (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Issues table
CREATE TABLE IF NOT EXISTS Issues (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL,
    priority VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Open',
    assigned_to INTEGER,
    created_by INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES Users(id),
    FOREIGN KEY (created_by) REFERENCES Users(id)
);

-- Insert admin user (password: password123)
INSERT INTO Users (firstname, lastname, email, password) 
VALUES (
    'Admin',
    'User',
    'admin@project2.com',
    '$2y$10$DbWtBAgqu9qTDuKtHv7g7uINuQxySwc2m9mbOwDWEfBK2TphFRFyy'
);

--Test issue 
INSERT INTO Issues (title, description, type, priority, status, assigned_to, created_by, created_at) 
VALUES ('Sample Issue', 'Test issue.', 'Bug', 'High', 'Open', 1, 1, NOW());


