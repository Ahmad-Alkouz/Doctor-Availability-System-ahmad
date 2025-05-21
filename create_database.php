<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL without selecting a database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS doctors");
    echo "Database 'doctors' created or already exists.<br>";
    
    // Select the database
    $pdo->exec("USE doctors");
    echo "Database 'doctors' selected.<br>";
    
    // Now run the setup script
    echo "Running setup script...<br>";
    include 'setup_database.php';
    
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 