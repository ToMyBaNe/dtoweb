<?php
// Database Configuration for MySQL/MariaDB
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dtoweb');

// Create or connect to MySQL database with optimizations
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            // First, try to connect without database to create it if needed
            $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
            $temp_pdo = new PDO($dsn, DB_USER, DB_PASS);
            $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            unset($temp_pdo);
            
            // Now connect to the specific database
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // MySQL optimizations
            $pdo->exec("SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Initialize database tables if they don't exist
function initializeDB() {
    $pdo = getDB();
    
    // Announcements table
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        image VARCHAR(255),
        date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        active TINYINT DEFAULT 1,
        INDEX idx_active (active),
        INDEX idx_date_created (date_created)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Add image column to announcements if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE announcements ADD COLUMN image VARCHAR(255) AFTER content");
    } catch (Exception $e) {
        // Column already exists, ignore error
    }
    
    // News table
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        image VARCHAR(255),
        date_published DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        active TINYINT DEFAULT 1,
        featured TINYINT DEFAULT 0,
        INDEX idx_active (active),
        INDEX idx_date_published (date_published)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Add image column to news if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE news ADD COLUMN image VARCHAR(255) AFTER content");
    } catch (Exception $e) {
        // Column already exists, ignore error
    }
    
    // Admin users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Calendar events table
    $pdo->exec("CREATE TABLE IF NOT EXISTS calendar_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description LONGTEXT,
        event_date DATE NOT NULL,
        start_time TIME,
        end_time TIME,
        location VARCHAR(255),
        color VARCHAR(7) DEFAULT '#6b1212',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        active TINYINT DEFAULT 1,
        INDEX idx_active (active),
        INDEX idx_event_date (event_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Systems table
    $pdo->exec("CREATE TABLE IF NOT EXISTS systems (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description LONGTEXT,
        url VARCHAR(500) NOT NULL,
        logo VARCHAR(255),
        icon_color VARCHAR(7) DEFAULT '#6b1212',
        display_order INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        active TINYINT DEFAULT 1,
        INDEX idx_active (active),
        INDEX idx_display_order (display_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Check if default admin exists, if not create one
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'admin@dtoweb.com']);
    }
}

// Start sessions only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'use_strict_mode' => 1,
        'use_cookies' => 1,
        'cookie_lifetime' => 0,
    ]);
}
?>
