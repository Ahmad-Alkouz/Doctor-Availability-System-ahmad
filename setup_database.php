<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

function executeSQLFile($pdo, $filename) {
    echo "Processing $filename...<br>";
    $sql = file_get_contents($filename);
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...<br>";
            } catch (PDOException $e) {
                echo "Error executing statement: " . $e->getMessage() . "<br>";
                echo "Statement: " . $statement . "<br><br>";
            }
        }
    }
}

try {
    // Drop existing tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS password_resets");
    $pdo->exec("DROP TABLE IF EXISTS doctors");
    $pdo->exec("DROP TABLE IF EXISTS students");
    echo "Dropped existing tables if any.<br><br>";

    // Execute doctors.sql first
    executeSQLFile($pdo, 'doctors.sql');
    echo "<br>Doctors table created successfully.<br>";

    // Execute students.sql
    executeSQLFile($pdo, 'students.sql');
    echo "<br>Students table created successfully.<br>";

    // Execute password_resets.sql last (because it has foreign key constraints)
    executeSQLFile($pdo, 'password_resets.sql');
    echo "<br>Password resets table created successfully.<br>";

    echo "<br>All tables have been created successfully! You can now <a href='index.php'>go to the home page</a>.";
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 