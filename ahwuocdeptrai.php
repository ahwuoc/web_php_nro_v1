<?php
// Load .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database config
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? '';
$DB_USER = $_ENV['DB_USER'] ?? '';
$DB_PASS = $_ENV['DB_PASS'] ?? '';

// Tạo kết nối PDO riêng
try {
    $conn = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$BALANCE_FIELD = $_ENV['BALANCE_FIELD'] ?? 'vnd';
$TOTAL_NAP_FIELD = $_ENV['TOTAL_NAP_FIELD'] ?? 'tongnap';
$NAP_PREFIX = $_ENV['NAP_PREFIX'] ?? 'NAP';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$logFile = 'sepay.log';
$logData = date('Y-m-d H:i:s') . ' - ' . $input . "\n";
file_put_contents($logFile, $logData, FILE_APPEND);

if (!$data || !isset($data['content']) || !isset($data['transferAmount']) || !isset($data['transferType'])) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Invalid data']));
}
if ($data['transferType'] !== 'in') {
    exit(json_encode(['success' => true, 'message' => 'Ignored outgoing transaction']));
}

$content = strtoupper(trim($data['content']));
$amount = intval($data['transferAmount']);
$transactionId = $data['id'] ?? null;
$referenceCode = $data['referenceCode'] ?? null;

if (strpos($content, $NAP_PREFIX) !== 0) {
    exit(json_encode(['success' => true, 'message' => "Content not match $NAP_PREFIX format"]));
}
$afterPrefix = trim(substr($content, strlen($NAP_PREFIX)));
$parts = explode(' ', $afterPrefix);
$username = strtolower($parts[0]); 

try {
    $stmt = $conn->prepare("SELECT id, username FROM account WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - User not found: $username\n", FILE_APPEND);
        exit(json_encode(['success' => false, 'message' => 'User not found']));
    }

    if ($transactionId) {
        $stmt = $conn->prepare("SELECT id FROM nap_tien WHERE transaction_id = :trans_id");
        $stmt->bindParam(':trans_id', $transactionId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->fetch()) {
            exit(json_encode(['success' => true, 'message' => 'Transaction already processed']));
        }
    }
    $conn->beginTransaction();
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
