<?php
require_once 'core/set.php';
require_once 'core/connect.php';
$_alert = null;
require_once 'core/head.php';
if ($_login === null) {
    echo '<script>window.location.href = "dang-nhap.php";</script>';
}

// Cấu hình ngân hàng MBBank
$bank_account = $_ENV['BANK_ACCOUNT'] ?? "0862267487";
$bank_name = $_ENV['BANK_NAME'] ?? "MB"; // Mã ngân hàng MBBank trên Sepay
$bank_name_display = $_ENV['BANK_DISPLAY_NAME'] ?? "MBBank (Quân Đội)";
$account_name = $_ENV['BANK_ACCOUNT_NAME'] ?? "LE MINH NHUT";
$nap_prefix = $_ENV['NAP_PREFIX'] ?? "NAP";

// Tạo nội dung chuyển khoản
$transfer_content = $nap_prefix . ($_username ?? 'GUEST');
?>

<style>
    /* Styling for Dark/Orange Theme */
    .qr-container {
        text-align: center;
        padding: 20px;
        color: #e0e0e0;
    }
    .qr-code {
        max-width: 250px;
        border: 4px solid #ff5722;
        border-radius: 10px;
        margin: 15px auto;
        background: white; /* White background essential for QR scanning */
        padding: 10px;
        box-shadow: 0 0 15px rgba(255, 87, 34, 0.3);
    }
    .bank-info {
        background: rgba(0, 0, 0, 0.4);
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        border: 1px solid #444;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    .bank-info p {
        margin: 12px 0;
        font-size: 1.1em;
        color: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px dashed #555;
        padding-bottom: 8px;
    }
    .bank-info p:last-child {
        border-bottom: none;
    }
    .bank-info strong {
        color: #ffb74d; /* Light orange for labels */
        min-width: 140px;
    }
    .copy-btn {
        cursor: pointer;
        color: #e0e0e0;
        background: #ff5722;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.8em;
        font-weight: bold;
        transition: all 0.2s;
    }
    .copy-btn:hover {
        background: #f4511e;
        transform: scale(1.05);
    }
    .amount-select {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        margin: 20px 0;
    }
    .amount-btn {
        padding: 10px 25px;
        border: 2px solid #ff5722;
        border-radius: 8px;
        background: transparent;
        color: #e0e0e0;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: bold;
        min-width: 100px;
    }
    .amount-btn:hover, .amount-btn.active {
        background: #ff5722;
        color: white;
        box-shadow: 0 0 15px rgba(255, 87, 34, 0.6);
        transform: translateY(-2px);
    }
    #custom-amount {
        background: #2d2d2d;
        border: 2px solid #555;
        color: #fff;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        font-size: 1.1em;
    }
    #custom-amount:focus {
        border-color: #ff5722;
        outline: none;
        box-shadow: 0 0 8px rgba(255, 87, 34, 0.4);
    }
    .page-title-nap {
        color: #ff5722;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 25px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        font-size: 2em;
    }
    .nav-history {
        color: #bbb;
        text-decoration: none;
        transition: color 0.2s;
        font-size: 1.1em;
    }
    .nav-history:hover {
        color: #ff9800;
    }
    .alert-custom {
        background: rgba(45, 45, 45, 0.9);
        border: 1px solid #ff9800;
        color: #fff;
        border-radius: 8px;
    }
    .btn-confirm {
        background: #ff5722;
        border: none;
        padding: 12px 40px;
        font-size: 1.2em;
        font-weight: bold;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(255, 87, 34, 0.4);
        width: 100%;
        max-width: 400px;
    }
    .btn-confirm:hover {
        background: #e64a19;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 87, 34, 0.6);
    }
</style>

<div class="ant-col ant-col-xs-24 ant-col-sm-24 ant-col-md-24">
    <div class="page-layout-body">
        <div class="ant-row">
            <a href="/" style="color: #bbb" class="ant-col ant-col-24 home_page_bodyTitleList__UdhN_">
                <i class="fas fa-arrow-left"></i> Quay lại diễn đàn
            </a>
        </div>
        <div class="ant-col ant-col-24">
            <div class="container pt-3 pb-5">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="text-center pb-4">
                            <a href="history.php" class="nav-history">
                                <i class="fas fa-history"></i> Xem lịch sử nạp tiền
                            </a>
                        </div>
                        <h4 class="text-center page-title-nap">NẠP SỐ DƯ QUA MBBANK</h4>
                        
                        <?php if ($_login === null) { ?>
                            <div class="alert alert-danger text-center">
                                Bạn chưa đăng nhập? Hãy đăng nhập để sử dụng chức năng này
                            </div>
                        <?php } else { ?>
                        
                        <div class="bank-info">
                            <p>
                                <span><strong>Ngân hàng:</strong> <?php echo $bank_name_display; ?></span>
                            </p>
                            <p>
                                <span><strong>Số tài khoản:</strong> <span style="font-size:1.2em; color:white"><?php echo $bank_account; ?></span></span>
                                <span class="copy-btn" onclick="copyText('<?php echo $bank_account; ?>')"><i class="fas fa-copy"></i> Copy</span>
                            </p>
                            <p>
                                <span><strong>Chủ tài khoản:</strong> <span style="text-transform:uppercase"><?php echo $account_name; ?></span></span>
                            </p>
                            <p>
                                <span><strong>Nội dung CK:</strong> <span id="transfer-content" style="color:#ff5722; font-weight:bold"><?php echo $transfer_content; ?></span></span>
                                <span class="copy-btn" onclick="copyText('<?php echo $transfer_content; ?>')"><i class="fas fa-copy"></i> Copy</span>
                            </p>
                        </div>

                        <div class="text-center">
                            <label style="color: #e0e0e0; font-size: 1.1em; margin-bottom: 10px;">Thay đổi số tiền nạp nhanh:</label>
                            <div class="amount-select">
                                <button class="amount-btn" data-amount="10000">10.000đ</button>
                                <button class="amount-btn" data-amount="20000">20.000đ</button>
                                <button class="amount-btn" data-amount="50000">50.000đ</button>
                                <button class="amount-btn" data-amount="100000">100.000đ</button>
                                <button class="amount-btn" data-amount="200000">200.000đ</button>
                                <button class="amount-btn" data-amount="500000">500.000đ</button>
                            </div>
                            <input type="number" id="custom-amount" class="form-control mt-3" placeholder="Hoặc nhập số tiền tùy ý (tối thiểu 10.000đ)..." min="10000" step="1000">
                        </div>

                        <div class="qr-container">
                            <p style="font-size: 1.1em"><strong>Quét mã QR bằng App Ngân Hàng:</strong></p>
                            <img id="qr-code" class="qr-code" src="https://qr.sepay.vn/img?acc=<?php echo $bank_account; ?>&bank=<?php echo $bank_name; ?>&amount=10000&des=<?php echo urlencode($transfer_content); ?>" alt="QR Code">
                        </div>

                        <div class="alert alert-custom text-center mb-4">
                            <i class="fas fa-exclamation-triangle" style="color: #ff9800"></i> <strong>Lưu ý:</strong> Vui lòng nhập đúng <strong>Nội dung chuyển khoản</strong> để được cộng tiền tự động 24/7.
                        </div>

                        <div class="text-center mt-3">
                            <button class="btn btn-confirm" onclick="confirmNap()">
                                <i class="fas fa-check-circle"></i> XÁC NHẬN ĐÃ CHUYỂN
                            </button>
                        </div>

                        <script>
                        let selectedAmount = 10000;

                        function confirmNap() {
                            const customAmount = document.getElementById('custom-amount').value;
                            const amount = customAmount >= 10000 ? customAmount : selectedAmount;
                            
                            Swal.fire({
                                title: 'Xác nhận nạp tiền',
                                html: `Bạn xác nhận đã chuyển khoản <strong>${Number(amount).toLocaleString()}đ</strong><br>với nội dung: <strong style='color:#ff5722'><?php echo $transfer_content; ?></strong>?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#ff5722',
                                cancelButtonColor: '#444',
                                confirmButtonText: 'Đúng, tôi đã chuyển',
                                cancelButtonText: 'Chưa, quay lại'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Gọi API lưu yêu cầu nạp tiền
                                    fetch('api/nap-tien.php', {
                                        method: 'POST',
                                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                        body: 'amount=' + amount
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            Swal.fire({
                                                title: 'Thành công!',
                                                html: 'Hệ thống đang xử lý giao dịch.<br>Vui lòng chờ 1-3 phút để cộng tiền.',
                                                icon: 'success',
                                                timer: 3000,
                                                showConfirmButton: false,
                                                background: '#2d2d2d',
                                                color: '#fff'
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Thông báo',
                                                text: data.message,
                                                icon: 'error',
                                                background: '#2d2d2d',
                                                color: '#fff'
                                            });
                                        }
                                    })
                                    .catch(err => {
                                        Swal.fire('Lỗi', 'Không thể kết nối server', 'error');
                                    });
                                }
                            });
                        }

                        function copyText(text) {
                            navigator.clipboard.writeText(text).then(function() {
                                Swal.fire({
                                    title: 'Đã copy!',
                                    text: text,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#2d2d2d',
                                    color: '#fff',
                                    toast: true,
                                    position: 'top-end'
                                });
                            });
                        }

                        // Xử lý chọn số tiền
                        document.querySelectorAll('.amount-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
                                selectedAmount = this.dataset.amount;
                                updateQR(this.dataset.amount);
                                document.getElementById('custom-amount').value = '';
                            });
                        });

                        // Xử lý nhập số tiền tùy chỉnh
                        document.getElementById('custom-amount').addEventListener('input', function() {
                            if (this.value >= 10000) {
                                document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
                                updateQR(this.value);
                            }
                        });

                        function updateQR(amount) {
                            const qrUrl = `https://qr.sepay.vn/img?acc=<?php echo $bank_account; ?>&bank=<?php echo $bank_name; ?>&amount=${amount}&des=<?php echo urlencode($transfer_content); ?>`;
                            document.getElementById('qr-code').src = qrUrl;
                        }
                        </script>

                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'core/footer.php'; ?>
</body>
</html>
