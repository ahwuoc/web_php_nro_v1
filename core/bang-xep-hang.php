<?php
include ('head.php');
?>

<style>
    .page-title {
        color: #ff5722;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        font-size: 1.8em;
        text-align: center;
    }
    .ranking-card {
        background: rgba(30, 30, 30, 0.95);
        border: 1px solid #444;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .ranking-header {
        color: #ff9800;
        font-weight: bold;
        text-transform: uppercase;
        border-bottom: 2px solid #ff5722;
        padding-bottom: 10px;
        margin-bottom: 15px;
        font-size: 1.2em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .table-custom {
        width: 100%;
        color: #e0e0e0;
        border-collapse: separate;
        border-spacing: 0 5px;
    }
    .table-custom th {
        background: #ff5722;
        color: white;
        padding: 12px;
        text-transform: uppercase;
        font-size: 0.9em;
        border: none;
    }
    .table-custom th:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
    .table-custom th:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .table-custom td {
        background: #3a3a3a;
        padding: 12px;
        font-weight: 500;
        border-top: 1px solid #444;
        border-bottom: 1px solid #444;
    }
    .table-custom tr td:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        border-left: 1px solid #444;
        font-weight: bold;
        color: #ff9800;
    }
    .table-custom tr td:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        border-right: 1px solid #444;
    }
    .table-custom tr:hover td {
        background: #444;
        transform: scale(1.01);
        transition: all 0.2s;
    }
    /* Top 3 Highlighting */
    .rank-1 { color: #FFD700 !important; font-size: 1.2em; } /* Gold */
    .rank-2 { color: #C0C0C0 !important; font-size: 1.1em; } /* Silver */
    .rank-3 { color: #CD7F32 !important; font-size: 1.1em; } /* Bronze */
    
    .rank-badge {
        display: inline-block;
        width: 25px;
        height: 25px;
        line-height: 25px;
        border-radius: 50%;
        text-align: center;
        background: #222;
        color: #fff;
        font-size: 0.8em;
    }
    .rank-1 .rank-badge { background: #FFD700; color: #000; box-shadow: 0 0 10px #FFD700; }
    .rank-2 .rank-badge { background: #C0C0C0; color: #000; }
    .rank-3 .rank-badge { background: #CD7F32; color: #000; }

    .planet-icon {
        width: 20px;
        vertical-align: middle;
        margin-right: 5px;
    }
</style>

<div class="page-layout-body">
    <div class="container pt-3">
        <!-- TOP SỨC MẠNH -->
        <div class="ranking-card">
            <h6 class="ranking-header"><i class="fas fa-fist-raised"></i> BẢNG XẾP HẠNG ĐUA TOP SỨC MẠNH</h6>
            <div class="table-responsive">
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Nhân vật</th>
                            <th width="30%">Đệ Tử</th>
                            <th width="25%">Sức Mạnh</th>
                            <th width="15%">Hành Tinh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT name, gender, pet,
                                CAST(JSON_UNQUOTE(JSON_EXTRACT(data_point, '$[1]')) AS SIGNED) AS second_value
                            FROM player
                            ORDER BY second_value DESC
                            LIMIT 10;";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();

                        $countTop = 1;
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $rankClass = '';
                                if($countTop == 1) $rankClass = 'rank-1';
                                elseif($countTop == 2) $rankClass = 'rank-2';
                                elseif($countTop == 3) $rankClass = 'rank-3';

                                $petHtml = '<span style="color:#777;">Chưa có</span>';
                                if (!empty($row['pet'])) {
                                    $petData = json_decode($row['pet'], true);
                                    if (is_array($petData) && count($petData) >= 2) {
                                        $petInfo = json_decode($petData[0], true);
                                        $petStats = json_decode($petData[1], true);
                                        
                                        // Check if pet type is valid (not -1)
                                        if (is_array($petInfo) && isset($petInfo[0]) && $petInfo[0] != -1 && isset($petInfo[2])) {
                                            $petName = $petInfo[2];
                                            $petPower = (is_array($petStats) && isset($petStats[1])) ? $petStats[1] : 0;
                                            
                                            // Format Pet Power
                                            $petPowerStr = '';
                                            if ($petPower > 1000000000) $petPowerStr = number_format($petPower / 1000000000, 1) . ' tỷ';
                                            elseif ($petPower > 1000000) $petPowerStr = number_format($petPower / 1000000, 1) . ' Tr';
                                            elseif ($petPower >= 1000) $petPowerStr = number_format($petPower / 1000, 1) . ' k';
                                            else $petPowerStr = number_format($petPower, 0, ',', '');
                                            
                                            $petHtml = '<div style="font-weight:bold; color: #00bcd4;">' . htmlspecialchars($petName) . '</div>';
                                            $petHtml .= '<div style="font-size: 0.85em; color: #ff9800;">SM: ' . $petPowerStr . '</div>';
                                        }
                                    }
                                }
                        ?>
                                <tr class="<?php echo $rankClass; ?>">
                                    <td><span class="rank-badge"><?php echo $countTop++; ?></span></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo $petHtml; ?></td>
                                    <td>
                                        <?php
                                        $value = $row['second_value'];
                                        if ($value != '') {
                                            if ($value > 1000000000) echo number_format($value / 1000000000, 1) . ' tỷ';
                                            elseif ($value > 1000000) echo number_format($value / 1000000, 1) . ' Tr';
                                            elseif ($value >= 1000) echo number_format($value / 1000, 1) . ' k';
                                            else echo number_format($value, 0, ',', '');
                                        } else {
                                            echo '0';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($row['gender'] == 0) echo '<span style="color:#4caf50">Trái Đất</span>';
                                        elseif ($row['gender'] == 1) echo '<span style="color:#8bc34a">Namec</span>';
                                        elseif ($row['gender'] == 2) echo '<span style="color:#FFD700">Xayda</span>';
                                        ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="5">Chưa có dữ liệu</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOP NẠP -->
        <div class="ranking-card">
            <h6 class="ranking-header"><i class="fas fa-gem"></i> BẢNG XẾP HẠNG ĐUA TOP NẠP</h6>
            <div class="table-responsive">
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="50%">Nhân vật</th>
                            <th width="40%">Tổng Nạp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT p.name, a.tongnap FROM player p JOIN account a ON p.account_id = a.id ORDER BY a.tongnap DESC LIMIT 10;";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();

                        $stt = 1;
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $rankClass = '';
                                if($stt == 1) $rankClass = 'rank-1';
                                elseif($stt == 2) $rankClass = 'rank-2';
                                elseif($stt == 3) $rankClass = 'rank-3';
                        ?>
                            <tr class="<?php echo $rankClass; ?>">
                                <td><span class="rank-badge"><?php echo $stt++; ?></span></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td style="color: #4caf50; font-weight: bold;"><?php echo number_format($row['tongnap'], 0, ','); ?>đ</td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="3">Chưa có thống kê!</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TOP NHIỆM VỤ -->
        <div class="ranking-card">
            <h6 class="ranking-header"><i class="fas fa-tasks"></i> BẢNG XẾP HẠNG ĐUA TOP NHIỆM VỤ</h6>
            <div class="table-responsive">
                <table class="table-custom text-center">
                    <thead>
                        <tr>
                            <th width="10%">#</th>
                            <th width="50%">Nhân vật</th>
                            <th width="40%">Nhiệm Vụ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT name, 
                                CAST(JSON_UNQUOTE(JSON_EXTRACT(data_task, '$[0]')) AS UNSIGNED) AS task_id
                            FROM player 
                            ORDER BY task_id DESC
                            LIMIT 20;";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();

                        $stt = 1;
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <tr>
                                <td><span class="rank-badge"><?php echo $stt++; ?></span></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>NV <?php echo number_format($row['task_id'], 0, ','); ?></td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="3">Chưa có thống kê!</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="text-right mt-3" style="font-size: 0.9em; color: #888;">
                <i class="far fa-clock"></i> Cập nhật lúc: <?php echo date('H:i d/m/Y'); ?>
            </div>
        </div>
    </div>
</div>

<div style="line-height:15px;font-size:12px;padding-bottom:10px;padding-top:6px;text-align:center; color: #bbb;">
    <img height="12" src="/public/images/12.png" style="vertical-align:middle" />
    <span style="vertical-align:middle">Dành cho người chơi trên 12 tuổi. Chơi quá 180 phút mỗi ngày sẽ hại sức khỏe.</span><br /><br />
    <div>
        <h5>
            2024© Được vận hành bởi <a href="<?php echo $_zalolink; ?>" style="color: #ff9800;"><?php echo $_tenmaychu; ?></a>
        </h5>
    </div>
</div>
</body>
</html>