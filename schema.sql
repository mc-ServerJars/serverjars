-- MySQL database schema for Minecraft server jars tracking system
-- This schema supports tracking multiple forks (Paper, Purpur, etc.) and their versions

CREATE DATABASE IF NOT EXISTS minecraft_jars;
USE minecraft_jars;

-- Main table to store version information
CREATE TABLE IF NOT EXISTS minecraft_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fork VARCHAR(50) NOT NULL COMMENT 'The fork name (e.g., Paper, Purpur, Vanilla)',
    game_version VARCHAR(20) NOT NULL COMMENT 'Minecraft version (e.g., 1.20.1)',
    build_number VARCHAR(20) NOT NULL COMMENT 'Build number (could be numeric or other identifier)',
    name VARCHAR(255) NOT NULL COMMENT 'Build name/identifier',
    download_link VARCHAR(512) NOT NULL COMMENT 'URL to download the jar file',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When this record was created',
    
    -- Indexes for faster lookups
    INDEX idx_fork (fork),
    INDEX idx_game_version (game_version),
    INDEX idx_fork_version (fork, game_version),
    
    -- Ensure we don't have duplicate builds
    UNIQUE KEY uniq_fork_version_name (fork, game_version, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores Minecraft server jar versions across different forks';
