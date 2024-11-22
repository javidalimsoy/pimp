<?php
// Database connection settings
$host = "localhost";
$dbname = "rolex_honeypot";
$username = "root";
$password = "root";

try {
    $conn = new PDO("mysql:host=localhost;dbname=rolex_honeypot", "root", "root");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

