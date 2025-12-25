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

// Hàm lưu log vào database
function logToDatabase($conn, $type, $message, $data = null) {
    try {
        // Tạo table nếu chưa tồn tại
        $conn->exec("CREATE TABLE IF NOT EXISTS `sepay_log` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `type` VARCHAR(50) NOT NULL,
            `message` TEXT NOT NULL,
            `data` LONGTEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_type` (`type`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $stmt = $conn->prepare("INSERT INTO sepay_log (type, message, data) VALUES (:type, :message, :data)");
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':data', $data, PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        // Nếu lỗi, ghi vào file backup
        file_put_contents('sepay_error.log', date('Y-m-d H:i:s') . " - DB Log Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}
$input = file_get_contents('php://input');
$data = json_decode($input, true);
logToDatabase($conn, 'request', 'Received callback', $input);

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

$napPos = strpos($content, $NAP_PREFIX);
if ($napPos === false) {
    exit(json_encode(['success' => true, 'message' => "Content not match $NAP_PREFIX format"]));
}
$afterPrefix = substr($content, $napPos + strlen($NAP_PREFIX));
preg_match('/^([A-Za-z0-9]+)/', $afterPrefix, $matches);
if (empty($matches[1])) {
    exit(json_encode(['success' => false, 'message' => 'Username not found in content']));
}
$username = strtolower($matches[1]); 

try {
    $stmt = $conn->prepare("SELECT id, username FROM account WHERE username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        logToDatabase($conn, 'error', "User not found: $username", json_encode($data));
        exit(json_encode(['success' => false, 'message' => 'User not found']));
    }

    if ($transactionId) {
        $stmt = $conn->prepare("SELECT id FROM nap_tien WHERE transaction_id = :trans_id");
        $stmt->bindParam(':trans_id', $transactionId);
        $stmt->execute();
        if ($stmt->fetch()) {
            logToDatabase($conn, 'warning', "Transaction already processed: $transactionId", json_encode($data));
            exit(json_encode(['success' => true, 'message' => 'Transaction already processed']));
        }
    }
    $conn->beginTransaction();
    
    // Escape username để an toàn
    $escapedUsername = $conn->quote($username);
    $sql = "UPDATE account SET $BALANCE_FIELD = $BALANCE_FIELD + $amount, $TOTAL_NAP_FIELD = $TOTAL_NAP_FIELD + $amount WHERE username = $escapedUsername";
    $conn->exec($sql);

    // Lưu lịch sử nạp tiền
    $stmt = $conn->prepare("INSERT INTO nap_tien (username, amount, status, transaction_id, reference_code) VALUES (:username, :amount, 1, :trans_id, :ref_code)");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':trans_id', $transactionId);
    $stmt->bindParam(':ref_code', $referenceCode, PDO::PARAM_STR);
    $stmt->execute();

    $conn->commit();

    logToDatabase($conn, 'success', "Deposit successful: $username +$amount VND", json_encode($data));
    
    echo json_encode(['success' => true, 'message' => "Cộng $amount VND cho $username thành công"]);

} catch (Exception $e) {
    $conn->rollBack();
    logToDatabase($conn, 'error', 'Exception: ' . $e->getMessage(), json_encode($data));
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
