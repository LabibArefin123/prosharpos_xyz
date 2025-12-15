<?php
// config.php

// Safe session start
if (session_status() === PHP_SESSION_NONE) {
     session_start();
}

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CSRF token
if (empty($_SESSION['token'])) {
     $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Database config
$host = 'localhost';
$db   = 'tot_crm';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Connect to DB
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
     die("DB Connection failed: " . $e->getMessage());
}

// Define clean() if not already defined
if (!function_exists('clean')) {
     function clean($data)
     {
          return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
     }
}
