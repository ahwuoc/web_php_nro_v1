<?php
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

$ip_sv = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbname_sv = $_ENV['DB_NAME'] ?? 'nro';
$user_sv = $_ENV['DB_USER'] ?? 'root';
$pass_sv = $_ENV['DB_PASS'] ?? '';

date_default_timezone_set('Asia/Ho_Chi_Minh');

try {
    $dsn = "mysql:host=$ip_sv;dbname=$dbname_sv;charset=utf8mb4;sslmode=disabled";
    $conn = new PDO($dsn, $user_sv, $pass_sv, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
