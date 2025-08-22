<?php
// Database configuration
$host = 'localhost';
$db   = 'flexfusion_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Set up DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options for PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
];

// Attempt to connect
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // You can now use $pdo in any file that includes this one
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
