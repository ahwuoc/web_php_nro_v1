<?php
// Load environment variables from .env file
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

// Cấu Hình Cơ Bản
$_domain = 'https://chillnro.online'; // điền domain của sự kiện giới thiệu của bạn
$_IP = $_SERVER['REMOTE_ADDR']; // IP hiển thị ở phần cuối trang
$_tenmaychu = $_ENV['SERVER_NAME'] ?? 'Ngọc Rồng Chill'; // Tên máy chủ hiển thị ở cuối trang
$_mienmaychu = 'Tải Ngay ' . $_tenmaychu; // Tên hiển thị phần download
$_title = $_tenmaychu . ' - Máy Chủ Ngọc Rồng'; // Tên hiển thị phần header
$_dangnhap = 'Đăng Nhập ' . $_tenmaychu; // Tên hiển thị phần đăng nhập
$_dangky = 'Đăng Ký ' . $_tenmaychu; // Tên hiển thị phần đăng ký
$_zalolink = $_ENV['ZALO_LINK'] ?? ''; // Link Zalo group

// thông tin cấu hình vps
$serverIP = "127.0.0.1"; // lấy thông tin máy chủ vps
$serverPort = "446"; // port vps

// API RECAPTCHA
$w_api_recaptcha = "6Ld7cRIlAAAAAAXcz8uJJFt_YzS4HYGIL24rfzPh";
$w_api_recaptcha_private ="6Ld7cRIlAAAAAC30XJ7NQLBAII468lHgdcT11_5_";


// PHIÊN BẢN FILE GAME
$_android = '2.3.7';
$_windows = '2.3.7';
$_java = '2.3.0';
$_iphone = '2.3.7';
?>