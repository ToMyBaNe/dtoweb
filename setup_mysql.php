<?php
/**
 * MySQL Database Setup Script
 * Run this once to create the database and tables
 */

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `dtoweb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'dtoweb' created or already exists<br>";
    
    // Now connect to the new database
    $pdo = new PDO("mysql:host=$host;dbname=dtoweb;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create announcements table
    $pdo->exec("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        active TINYINT DEFAULT 1,
        INDEX idx_active (active),
        INDEX idx_date_created (date_created)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'announcements' created<br>";
    
    // Create news table
    $pdo->exec("CREATE TABLE IF NOT EXISTS news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT NOT NULL,
        date_published DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        active TINYINT DEFAULT 1,
        featured TINYINT DEFAULT 0,
        INDEX idx_active (active),
        INDEX idx_date_published (date_published)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'news' created<br>";
    
    // Create admin_users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_username (username)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ Table 'admin_users' created<br>";
    
    // Check if default admin exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'admin@dtoweb.com']);
        echo "✓ Default admin user created (username: admin, password: admin123)<br>";
    } else {
        echo "✓ Admin user already exists<br>";
    }
    
    // Try to migrate data from SQLite if database.db exists
    $sqlite_path = __DIR__ . '/database.db';
    if (file_exists($sqlite_path)) {
        echo "<br><strong>Migrating data from SQLite...</strong><br>";
        
        try {
            $sqlite_pdo = new PDO('sqlite:' . $sqlite_path);
            $sqlite_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Migrate announcements
            $stmt = $sqlite_pdo->query("SELECT * FROM announcements");
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($announcements) > 0) {
                foreach ($announcements as $row) {
                    $insert = $pdo->prepare("INSERT INTO announcements (title, content, date_created, date_updated, active) 
                                           VALUES (?, ?, ?, ?, ?)");
                    $insert->execute([
                        $row['title'],
                        $row['content'],
                        $row['date_created'],
                        $row['date_updated'],
                        $row['active']
                    ]);
                }
                echo "✓ Migrated " . count($announcements) . " announcement(s)<br>";
            }
            
            // Migrate news
            $stmt = $sqlite_pdo->query("SELECT * FROM news");
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($news) > 0) {
                foreach ($news as $row) {
                    $insert = $pdo->prepare("INSERT INTO news (title, content, date_published, date_updated, active, featured) 
                                           VALUES (?, ?, ?, ?, ?, ?)");
                    $insert->execute([
                        $row['title'],
                        $row['content'],
                        $row['date_published'],
                        $row['date_updated'],
                        $row['active'],
                        $row['featured']
                    ]);
                }
                echo "✓ Migrated " . count($news) . " news article(s)<br>";
            }
            
            echo "<br><strong>Migration complete!</strong><br>";
        } catch (Exception $e) {
            echo "⚠ Could not migrate SQLite data: " . $e->getMessage() . "<br>";
            echo "You can manually add your content through the admin panel.<br>";
        }
    }
    
    echo "<br><p style='color: green;'><strong>✓ MySQL setup completed successfully!</strong></p>";
    echo "<p><a href='/dtoweb/'>Return to website</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Make sure MySQL/MariaDB is running and the credentials in this file are correct.</p>";
    exit;
}
?>
