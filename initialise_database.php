<?php
/**
 * Simple Database Setup Script
 * 
 * Creates a database and user_registration table with basic attributes
 */

// Database configuration 
$host = "localhost";
$username = "root";//change if needed
$password = "";//change if needed
$dbname = "user_registration";


// Create connection to MySQL without selecting a database
try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    $conn->exec($sql);
    echo "Database created successfully<br>";
    
    // Connect to the new database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create user_registration table
    $sql = "CREATE TABLE IF NOT EXISTS user_registration (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL ,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        profile_pic VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "Table user_registration created successfully";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>