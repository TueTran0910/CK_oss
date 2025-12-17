<?php
include 'db.php';

// XỬ LÝ THÊM ĐIỂM
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mon = trim($_POST['mon_hoc']);
    $diem = $_POST['diem_so'];
    
    if(!empty($mon) && is_numeric($diem)){
        // Sử dụng Prepared Statement để bảo mật
        $stmt = $conn->prepare("INSERT INTO diem_thi (mon_hoc, diem_so) VALUES (?, ?)");
        $stmt->bind_param("sd", $mon, $diem);
        
        if($stmt->execute()){
            $msg = "<div class='message success'>✅ Đã thêm môn <b>$mon</b> thành công!</div>";
        } else {
            $msg = "<div class='message error'>❌ Lỗi: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}

// LẤY DANH SÁCH
$result = $conn->query("SELECT * FROM diem_thi ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Điểm Thi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <div class="profile-section">
            <h1>Hồ Sơ Sinh Viên</h1>
            <p><strong>Trần Thiên Tuệ</strong> - MSSV: <strong>DH52201727</strong></p>
            <p>Lớp: D22_TH10 | Đại Học Công Nghệ Sài Gòn</p>
        </div>

        <?php echo $msg; ?>

        <h3>➕ Nhập điểm mới</h3>
        <form method="POST" class="input-group">
            <input type="text" name="mon_hoc" placeholder="Nhập tên môn học..." required>
            <input type="number" name="diem_so" step="0.1" min="0" max="10" placeholder="Điểm số (0-10)" required>
            <button type="submit" class="btn-add">Lưu Lại</button>
        </form>

        <h3>📋 Bảng điểm chi tiết</h3>
        <table>
            <thead>
                <tr>
                    <th width="40%">Môn Học</th>
                    <th width="20%">Điểm Số</th>
                    <th width="25%">Ngày Nhập</th>
                    <th width="15%">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['mon_hoc']); ?></strong></td>
                        <td>
                            <?php 
                                $d = $row['diem_so'];
                                $color = ($d >= 5) ? '#059669' : '#dc2626'; // Xanh nếu đậu, Đỏ nếu rớt
                                echo "<span style='color:$color; font-weight:bold'>$d</span>";
                            ?>
                        </td>
                        <td style="color: #6b7280; font-size: 0.9em;">
                            <?php echo date("d/m/Y", strtotime($row['ngay_nhap'])); ?>
                        </td>
                        <td>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" 
                               class="action-btn btn-delete"
                               onclick="return confirm('Bạn có chắc muốn xóa môn này không?')">
                               Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 20px; color: #9ca3af;">Chưa có dữ liệu điểm thi nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>