<?php
/**
 * Callback xử lý webhook từ SePay
 * Nhận thông báo giao dịch ngân hàng và tự động cộng tiền
 */

require_once 'core/connect.php';

// Lấy cấu hình từ ENV
$BALANCE_FIELD = $_ENV['BALANCE_FIELD'] ?? 'vnd';
$TOTAL_NAP_FIELD = $_ENV['TOTAL_NAP_FIELD'] ?? 'tongnap';
$NAP_PREFIX = $_ENV['NAP_PREFIX'] ?? 'NAP';

// Chỉ chấp nhận POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Lấy dữ liệu JSON từ request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log request để debug
$logFile = 'sepay.log';
$logData = date('Y-m-d H:i:s') . ' - ' . $input . "\n";
file_put_contents($logFile, $logData, FILE_APPEND);

// Validate dữ liệu
if (!$data || !isset($data['content']) || !isset($data['transferAmount']) || !isset($data['transferType'])) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Invalid data']));
}

// Chỉ xử lý giao dịch tiền vào
if ($data['transferType'] !== 'in') {
    exit(json_encode(['success' => true, 'message' => 'Ignored outgoing transaction']));
}

$content = strtoupper(trim($data['content']));
$amount = intval($data['transferAmount']);
$transactionId = $data['id'] ?? null;
$referenceCode = $data['referenceCode'] ?? null;

// Kiểm tra nội dung có format NAP+username không
if (strpos($content, $NAP_PREFIX) !== 0) {
    exit(json_encode(['success' => true, 'message' => "Content not match $NAP_PREFIX format"]));
}

// Tách username từ nội dung (NAP-username hoặc NAP+username)
$parts = preg_split('/[-+]/', $content, 2);
if (count($parts) < 2 || empty($parts[1])) {
    exit(json_encode(['success' => false, 'message' => 'Invalid content format']));
}

$username = trim($parts[1]);

try {
    // Kiểm tra user có tồn tại không
    $stmt = $conn->prepare("SELECT id, username FROM account WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - User not found: $username\n", FILE_APPEND);
        exit(json_encode(['success' => false, 'message' => 'User not found']));
    }

    // Kiểm tra giao dịch đã xử lý chưa (tránh duplicate)
    if ($transactionId) {
        $stmt = $conn->prepare("SELECT id FROM nap_tien WHERE transaction_id = :trans_id");
        $stmt->bindParam(':trans_id', $transactionId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetch()) {
            exit(json_encode(['success' => true, 'message' => 'Transaction already processed']));
        }
    }

    // Bắt đầu transaction
    $conn->beginTransaction();

    // Cộng tiền cho user
    $stmt = $conn->prepare("UPDATE account SET $BALANCE_FIELD = $BALANCE_FIELD + :amount, $TOTAL_NAP_FIELD = $TOTAL_NAP_FIELD + :amount WHERE username = :username");
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    // Lưu lịch sử nạp tiền
    $stmt = $conn->prepare("INSERT INTO nap_tien (username, amount, status, transaction_id, reference_code) VALUES (:username, :amount, 1, :trans_id, :ref_code)");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':trans_id', $transactionId, PDO::PARAM_INT);
    $stmt->bindParam(':ref_code', $referenceCode, PDO::PARAM_STR);
    $stmt->execute();

    $conn->commit();

    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Success: $username +$amount VND\n", FILE_APPEND);
    
    echo json_encode(['success' => true, 'message' => "Cộng $amount VND cho $username thành công"]);

} catch (Exception $e) {
    $conn->rollBack();
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
