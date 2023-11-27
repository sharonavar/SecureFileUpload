<?php
require_once 'config.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
mysqli_query($conn, $sql);

mysqli_close($conn);

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create user table
$sql = "CREATE TABLE IF NOT EXISTS user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";
mysqli_query($conn, $sql);

// Create uploads table
$sql = "CREATE TABLE IF NOT EXISTS uploads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    filename VARCHAR(255),
    file_path VARCHAR(255),  -- Add a new column for file path
    upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id)
)";
mysqli_query($conn, $sql);

// Create logs table
$sql = "CREATE TABLE IF NOT EXISTS logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    filename VARCHAR(255),
    ip_address VARCHAR(45),
    upload_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'rejected', 'suspicious') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id)
)";
mysqli_query($conn, $sql);

mysqli_close($conn);

echo 'Database setup completed.';
?>
