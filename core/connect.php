<?php
$ip_sv = "103.77.172.200";
$dbname_sv = "nro_v1";
$user_sv = "ahwuocdz";
$pass_sv = "123456";

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
