-- SQL script to initialize the game database
-- Run this script in your MySQL or MariaDB server
-- It creates the `game` database and the tables used by the application

-- Create database (adjust name/charset if needed)
CREATE DATABASE IF NOT EXISTS game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE game;

-- Table for uploaded games
CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    author VARCHAR(100) NOT NULL,
    folder_name VARCHAR(100) UNIQUE NOT NULL,
    category VARCHAR(50),
    age_group VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved BOOLEAN DEFAULT FALSE,
    total_votes INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00
);

-- Table to store user votes
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    user_ip VARCHAR(45),
    rating INT CHECK (rating >= 1 AND rating <= 5),
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (game_id, user_ip)
);
