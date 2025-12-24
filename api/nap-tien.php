<?php
require_once '../core/set.php';
require_once '../core/connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

if ($_login === null) {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
    exit;
}

$amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
$transfer_content = "NAP-" . $_username;

if ($amount < 10000) {
    echo json_encode(['status' => 'error', 'message' => 'Số tiền tối thiểu là 10.000đ']);
    exit;
}

try {
    // Lưu yêu cầu nạp tiền
    $stmt = $conn->prepare("INSERT INTO nap_tien (username, amount, transfer_content, status) VALUES (:username, :amount, :transfer_content, 0)");
    $stmt->bindParam(':username', $_username);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':transfer_content', $transfer_content);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Đã ghi nhận yêu cầu nạp tiền. Vui lòng chờ hệ thống xử lý.',
            'data' => [
                'id' => $conn->lastInsertId(),
                'amount' => $amount,
                'transfer_content' => $transfer_content
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lưu yêu cầu']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi database: ' . $e->getMessage()]);
}
?>
