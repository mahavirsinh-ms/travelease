<?php
// TravelEASE database configuration.
// Local development falls back to the original XAMPP defaults;
// Render values are supplied through environment variables.
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'travelease';
$port = (int) (getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    http_response_code(500);
    die('Database connection failed.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
