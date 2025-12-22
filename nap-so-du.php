<?php
require_once 'core/set.php';
require_once 'core/connect.php';
$_alert = null;
require_once 'core/head.php';
if ($_login === null) {
    echo '<script>window.location.href = "dang-nhap.php";</script>';
}

// Cấu hình ngân hàng MBBank
$bank_account = "0368833697";
$bank_name = "MB"; // Mã ngân hàng MBBank trên Sepay
$account_name = "LE MINH NHUT";

// Tạo nội dung chuyển khoản
$transfer_content = "NAP " . ($_username ?? 'GUEST');
?>

<style>
    .qr-container {
        text-align: center;
        padding: 20px;
    }
    .qr-code {
        max-width: 250px;
        border: 2px solid #ddd;
        border-radius: 10px;
        margin: 15px auto;
    }
    .bank-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin: 15px 0;
    }
    .bank-info p {
        margin: 8px 0;
    }
    .copy-btn {
        cursor: pointer;
        color: #007bff;
        margin-left: 10px;
    }
    .amount-select {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        margin: 15px 0;
    }
    .amount-btn {
        padding: 10px 20px;
        border: 2px solid #007bff;
        border-radius: 5px;
        background: white;
        cursor: pointer;
        transition: all 0.3s;
    }
    .amount-btn:hover, .amount-btn.active {
        background: #007bff;
        color: white;
    }
</style>

<div class="ant-col ant-col-xs-24 ant-col-sm-24 ant-col-md-24">
    <div class="page-layout-body">
        <div class="ant-row">
            <a href="/" style="color: black" class="ant-col ant-col-24 home_page_bodyTitleList__UdhN_">Quay lại diễn đàn</a>
        </div>
        <div class="ant-col ant-col-24">
            <div class="container pt-3 pb-5">
                <div class="row">
                    <div class="col-lg-6 offset-lg-3">
                        <div class="text-center pb-3">
                            <a href="history.php" class="text-dark">
                                <i class="fas fa-hand-point-right"></i> Lịch sử nạp <i class="fas fa-hand-point-left"></i>
                            </a>
                        </div>
                        <h4 class="text-center">NẠP SỐ DƯ QUA MBBANK</h4>
                        
                        <?php if ($_login === null) { ?>
                            <p class="text-center">Bạn chưa đăng nhập? Hãy đăng nhập để sử dụng chức năng này</p>
                        <?php } else { ?>
                        
                        <div class="bank-info">
                            <p><strong>Ngân hàng:</strong> MBBank - Ngân hàng TMCP Quân đội</p>
                            <p><strong>Số tài khoản:</strong> <?php echo $bank_account; ?> 
                                <span class="copy-btn" onclick="copyText('<?php echo $bank_account; ?>')">📋 Copy</span>
                            </p>
                            <p><strong>Chủ tài khoản:</strong> <?php echo $account_name; ?></p>
                            <p><strong>Nội dung CK:</strong> <span id="transfer-content"><?php echo $transfer_content; ?></span>
                                <span class="copy-btn" onclick="copyText('<?php echo $transfer_content; ?>')">📋 Copy</span>
                            </p>
                        </div>

                        <div class="text-center">
                            <label><strong>Chọn số tiền nạp:</strong></label>
                            <div class="amount-select">
                                <button class="amount-btn" data-amount="10000">10.000đ</button>
                                <button class="amount-btn" data-amount="20000">20.000đ</button>
                                <button class="amount-btn" data-amount="50000">50.000đ</button>
                                <button class="amount-btn" data-amount="100000">100.000đ</button>
                                <button class="amount-btn" data-amount="200000">200.000đ</button>
                                <button class="amount-btn" data-amount="500000">500.000đ</button>
                            </div>
                            <input type="number" id="custom-amount" class="form-control mt-2" placeholder="Hoặc nhập số tiền khác..." min="10000" step="1000">
                        </div>

                        <div class="qr-container">
                            <p><strong>Quét mã QR để chuyển khoản:</strong></p>
                            <img id="qr-code" class="qr-code" src="https://qr.sepay.vn/img?acc=<?php echo $bank_account; ?>&bank=<?php echo $bank_name; ?>&amount=10000&des=<?php echo urlencode($transfer_content); ?>" alt="QR Code">
                        </div>

                        <div class="alert alert-warning text-center">
                            <strong>Lưu ý:</strong> Vui lòng nhập đúng nội dung chuyển khoản để hệ thống tự động cộng tiền!
                        </div>

                        <div class="alert alert-info text-center">
                            <strong>Thông báo:</strong> Sau khi chuyển khoản thành công, vui lòng chờ hệ thống sẽ tự động cộng tiền vào tài khoản của bạn trong vài phút!
                        </div>

                        <script>
                        function copyText(text) {
                            navigator.clipboard.writeText(text).then(function() {
                                Swal.fire({
                                    title: 'Đã copy!',
                                    text: text,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            });
                        }

                        // Xử lý chọn số tiền
                        document.querySelectorAll('.amount-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
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
