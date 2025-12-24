-- Bảng lưu lịch sử nạp tiền
CREATE TABLE IF NOT EXISTS `nap_tien` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `amount` BIGINT DEFAULT 0,
    `status` TINYINT DEFAULT 0 COMMENT '0: Chờ xử lý, 1: Thành công, 2: Thất bại',
    `transaction_id` INT DEFAULT NULL COMMENT 'ID giao dịch từ SePay',
    `reference_code` VARCHAR(100) DEFAULT NULL COMMENT 'Mã tham chiếu ngân hàng',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_username` (`username`),
    INDEX `idx_status` (`status`),
    UNIQUE INDEX `idx_transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
