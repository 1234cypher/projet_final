<?php
// Test script to verify database connection works
require_once 'includes/config.php';
require_once 'includes/Database.php';

try {
    echo "Testing database connection...\n";
    echo "DB_NAME: " . DB_NAME . "\n";
    
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Database connection successful!\n";
    
    // Test a simple query
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables found: " . implode(', ', $tables) . "\n";
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
