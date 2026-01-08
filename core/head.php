<?php
require_once 'cauhinh.php';
require_once 'set.php';
require_once 'connect.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $_title; ?></title>
    <link rel="canonical" href="http://nrokong.online/" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://nrokong.online/" />
    <meta property="og:title" content="NGỌC RỒNG " />
    <meta property="og:description"
        content="Website chính thức của <?php echo $_tenmaychu; ?> – Game Bay Vien Ngoc Rong Mobile nhập vai trực tuyến trên máy tính và điện thoại về Game 7 Viên Ngọc Rồng hấp dẫn nhất hiện nay!" />
    <meta property="og:image" content="" />
    <link rel="shortcut icon" href="/public/images/TW.svg">
    <meta name="description"
        content="Website chính thức của <?php echo $_tenmaychu; ?> – Game Bay Vien Ngoc Rong Mobile nhập vai trực tuyến trên máy tính và điện thoại về Game 7 Viên Ngọc Rồng hấp dẫn nhất hiện nay!">
    <meta name="keywords" content="ngoc rong mobile, game ngoc rong, game 7 vien ngoc rong, game bay vien ngoc rong">
    <link rel="stylesheet" href="/public/dist/css/style.css">
    <link rel="stylesheet" href="/public/dist/css/main.css" />
    <link rel="stylesheet" href="/public/dist/css/main2.css" />
    <link rel="stylesheet" href="/public/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/dist/css/all.min.css" />
    <link rel="stylesheet" href="/public/dist/css/sweetalert2.min.css" />
    <link rel="stylesheet" href="/public/dist/css/notiflix-3.2.6.min.css" />
    <script src="/public/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/dist/js/jquery-3.6.0.min.js"></script>
    <script src="/public/dist/js/sweetalert2.min.js"></script>
    <script src="/public/dist/js/notiflix-3.2.6.min.js"></script>
    <link rel="stylesheet" href="/public/dist/css/eff.css" />
    <script src="/core/eff.js" type="text/javascript"></script>
    <script>
        const redirectLink = '<?php echo $_zalolink; ?>';
        document.addEventListener('keydown', function (event) {
            if (event.key === 'F12' || (event.ctrlKey && event.shiftKey && event.key === 'I')) {
                window.location.href = redirectLink;
                event.preventDefault();
            }
        });

        document.addEventListener('contextmenu', function (event) {
            window.location.href = redirectLink;
            event.preventDefault();
        });

        setInterval(blockDevTools, 1000);
    </script>
    <style>
        .bbbbb {

            position: fixed;
            height: 100%;
        }

        .easeload {
            opacity: 0;
            -webkit-transition: all 3s ease;
            -moz-transition: all 3s ease;
            -ms-transition: all 3s ease;
            -o-transition: all 3s ease;
        }
    </style>
</head>

<body>
<script type="text/javascript">
		var SCREEN_HEIGHT = window.innerHeight;
		show_img_halo(SCREEN_HEIGHT, Math.random() * (5 - 1))

		function show_img_halo(h, z) {
			for (let index = 1; index <= z; index++) {
				var container = document.getElementById(index);
				var hh = Math.random() * (h - 10);
				var xx = Math.random() * (600 - 0) + 600;
				container.innerHTML = `<img  class="easeload" width="50px" onload="this.style.opacity=0.6"  src="/image/1.gif" style="position: absolute;color: red;left:${xx}px;top:${hh}px">`;

			}
			setTimeout(() => {
				for (let index = 1; index <= z; index++) {
					var nnnn = document.getElementById(index);
					nnnn.innerHTML = '';
				}
				lop_lop()

			}, 4000);
		}

		function lop_lop() {
			show_img_halo(SCREEN_HEIGHT, Math.random() * (5 - 1))
		}


		(function($) {

			"use strict"

			$(function() {
				if ($('#backTop').length) {
					var scrollTrigger = 100, // px
						backToTop = function() {
							var scrollTop = $(window).scrollTop();
							if (scrollTop > scrollTrigger) {
								$('#backTop').addClass('show');
							} else {
								$('#backTop').removeClass('show');
							}
						};
					backToTop();
					$(window).on('scroll', function() {
						backToTop();
					});
					$('#backTop').on('click', function(e) {
						e.preventDefault();
						$('html,body').animate({
							scrollTop: 0
						}, 700);
					});
				}

			});

		})(jQuery);
	</script>
    <div class="bbbbb">
        <span id="1"></span>
        <span id="2"></span>
        <span id="3"></span>
        <span id="4"></span>
        <span id="5"></span>
    </div>
    <a href="#" id="backTop" class="show"><img id="backTopimg" src="/image/favicon-32x32.png" alt="top"> </a>
    <section class="ant-layout page-layout-color body-bg">
        <main class="ant-layout-content page-body page-layout-color">
            <div class="page-layout-content">
                <div class="ant-row ant-row-space-around">
                    <div class="ant-col page-layout-header ant-col-xs-24 ant-col-sm-24 ant-col-md-24">
                        <div class="page-layout-header-content">
                            <a href="/">
                                <img src="<?php echo $_ENV['LOGO_URL'] ?? '/image/logo.png'; ?>" class="header-logo"
                                    style="display: block;margin-left: auto;margin-right: auto;max-height: 150px; auto;max-width: 300px;">
                            </a>
                            <div>
                                <?php
                                if ($_login === null) {
                                    ?>
                                    <div class="container color-main2 pb-2">
                                        <div class="text-center">
                                            <div class="row">
                                                <div class="col pr-0">
                                                    <a type="button" href="/dang-nhap.php"
                                                        class="ant-btn ant-btn-default header-btn-login mt-3 me-2">
                                                        <span>Đăng nhập ngay</span>
                                                    </a>

                                                    <a type="button" href="/dang-ky.php"
                                                        class="ant-btn ant-btn-default header-btn-login mt-3">
                                                        <span>Đăng ký</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else {
                                    if ($_admin == 1) { // Kiểm tra quyền truy cập
                                        ?>
                                            <div class="container color-main2 pb-2">
                                                <div class="text-center">
                                                    <div class="row">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="container color-main pt-3 pb-4">
                                                <div class="text-center">
                                                    <div id="header-update"></div>

                                                    <div class="row ant-space ant-space-horizontal ant-space-align-center space-header-menu d-flex justify-content-center"
                                                        style="flex-wrap:wrap;margin-bottom:-10px">



                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="../pass2.php">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                        <b>Pass Cấp 2</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="../doi-mat-khau.php">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                        <b>Đổi Mật Khẩu</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="../logout.php">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                        <b>Logout</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            function updateRemainingTime() {
                                                                fetch('../api/cauhinh/api-head.php')
                                                                    .then(response => response.text())
                                                                    .then(data => {
                                                                        document.getElementById("header-update").innerHTML = data;
                                                                    })
                                                                    .catch(error => console.error(error));
                                                            }

                                                            setInterval(updateRemainingTime, 500); // Cập nhật mỗi giây (1000ms)
                                                        </script>

                                                    </div>
                                                </div>

                                                <?php
                                    } else { ?>
                                                <div class="container color-main2 pb-2">
                                                    <div class="text-center">
                                                        <div class="row">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="container color-main pt-3 pb-4">
                                                    <div class="text-center">
                                                        <!-- Trong phần HTML-->
                                                        <div id="header-update"></div>

                                                        <script>
                                                            // Sử dụng JavaScript và AJAX để gửi yêu cầu đến máy chủ và cập nhật nội dung của vùng hiển thị kết quả
                                                            function updateRemainingTime() {
                                                                var xhttp = new XMLHttpRequest();
                                                                xhttp.onreadystatechange = function () {
                                                                    if (this.readyState === 4 && this.status === 200) {
                                                                        // Nhận phản hồi từ máy chủ và cập nhật nội dung của vùng hiển thị kết quả
                                                                        document.getElementById("header-update").innerHTML = this.responseText;
                                                                    }
                                                                };
                                                                xhttp.open("GET", "../api/cauhinh/api-head.php", true); // Thay đổi đường dẫn đến tệp PHP xử lý
                                                                xhttp.send();
                                                            }

                                                            // Tự động cập nhật thời gian mỗi giây
                                                            setInterval(updateRemainingTime, 100);
                                                        </script>
                                                        <div class="row ant-space ant-space-horizontal ant-space-align-center space-header-menu d-flex justify-content-center"
                                                            style="flex-wrap:wrap;margin-bottom:-10px">
                                                            <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                                style="padding-bottom:10px">
                                                                <div>
                                                                    <a href="../profile.php">
                                                                        <button type="button"
                                                                            class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                            <b>Profile</b>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                                style="padding-bottom:10px">
                                                                <div>
                                                                    <a href="../pass2.php">
                                                                        <button type="button"
                                                                            class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                            <b>Pass Cấp 2</b>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                                style="padding-bottom:10px">
                                                                <div>
                                                                    <a href="../doi-mat-khau.php">
                                                                        <button type="button"
                                                                            class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                            <b>Đổi Mật Khẩu</b>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                                style="padding-bottom:10px">
                                                                <div>
                                                                    <a href="../logout.php">
                                                                        <button type="button"
                                                                            class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                            <b>Logout</b>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col text-center">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                    }
                                }
                                ?>
                                        </div>
                                        <div class="ant-col ant-col-xs-24 ant-col-sm-24 ant-col-md-24">
                                            <div class="ant-row ant-row-space-around ant-row-middle header-menu">
                                                <div class="ant-col ant-col-24">
                                                    <div class="row ant-space ant-space-horizontal ant-space-align-center space-header-menu d-flex justify-content-center"
                                                        style="flex-wrap:wrap;margin-bottom:-10px">
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="../mo-thanh-vien.php">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                        <b>Mở Thành Viên</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="../nap-so-du.php">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                        <b>Nạp Tiền</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="../core/bang-xep-hang.php">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item w-100 /recharge">
                                                                        <b>Bảng Xếp Hạng</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a
                                                                    href="<?php echo $_zalolink; ?>">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item w-100 /fanpage">
                                                                        <b>Fanpage</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                            style="padding-bottom:10px">
                                                            <div>
                                                                <a href="<?php echo $_zalolink; ?>">
                                                                    <button type="button"
                                                                        class="ant-btn ant-btn-default header-menu-item w-100 /fanpage">
                                                                        <b>ZALO</b>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <center class="ant-col home_page_bodyTitleList__UdhN_"><?php echo $_mienmaychu; ?>
                                    <!-- <img src="/public/images/TW.svg" style="max-height: 40px; max-width: 20%" />             <img src="/public/images/bjw.png" style="max-height: 25px; max-width: 20%" /> -->
                                </center>
                                <div class="ant-col ant-col-xs-24 ant-col-sm-24 ant-col-md-24">
                                    <div class="ant-row ant-row-space-around ant-row-middle header-menu">
                                        <div class="ant-col ant-col-24">
                                            <div class="row ant-space ant-space-horizontal ant-space-align-center space-header-menu d-flex justify-content-center"
                                                style="flex-wrap:wrap;margin-bottom:-10px">
                                                <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                    style="padding-bottom:10px">
                                                    <div>
                                                        <a href="../download/windows.php">
                                                            <button style="height:45px" type="button"
                                                                class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                <img src="/public/images/0hrzmer.png"
                                                                    style="width:97px" />
                                                            </button>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                    style="padding-bottom:10px">
                                                    <div>
                                                        <a href="../download/android.php">
                                                            <button style="height:45px" type="button"
                                                                class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                <img src="/public/images/RAGk2Dn.png"
                                                                    style="width:97px" />
                                                            </button>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="ant-space-item col-6 col-md-3 col-lg-2"
                                                    style="padding-bottom:10px">
                                                    <div>
                                                        <a href="../download/iphone.php">
                                                            <button style="height:45px" type="button"
                                                                class="ant-btn ant-btn-default header-menu-item header-menu-item-active w-100">
                                                                <img src="/public/images/XnpBrRa.png"
                                                                    style="width:97px" />
                                                            </button>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>