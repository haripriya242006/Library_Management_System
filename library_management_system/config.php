<?php
// Update these settings to match your local MySQL installation.
$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = ''; // XAMPP default is usually blank. Set your MySQL password if needed.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and import database.sql. Details: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect_to(string $url): never {
    header('Location: ' . $url);
    exit;
}
