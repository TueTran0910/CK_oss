<?php
include 'db.php';

// XỬ LÝ: Thêm điểm khi bấm nút
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mon = $_POST['mon_hoc'];
    $diem = $_POST['diem_so'];
    
    if(!empty($mon) && is_numeric($diem)){
        $stmt = $conn->prepare("INSERT INTO diem_thi (mon_hoc, diem_so) VALUES (?, ?)");
        $stmt->bind_param("sd", $mon, $diem);
        if($stmt->execute()){
            $msg = "<p style='color:green'>Thêm thành công!</p>";
        } else {
            $msg = "<p style='color:red'>Lỗi SQL</p>";
        }
    }
}

// XỬ LÝ: Lấy danh sách điểm
$result = $conn->query("SELECT * FROM diem_thi ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Web Cá Nhân</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>👨‍🎓 Giới thiệu bản thân</h1>
        <p>Tên: Trần Thiên Tuệ</p>
        <p>MSSV: DH52201727</p>
        <p>Lớp D22_TH10</p>
        <p>Trường Đại Học Công Nghệ Sài Gòn</p>
        <hr>
        
        <h3>Nhập điểm thi</h3>
        <?php echo $msg; ?>
        <form method="POST">
            <input type="text" name="mon_hoc" placeholder="Tên môn học" required>
            <input type="number" name="diem_so" step="0.1" placeholder="Điểm số" required>
            <button type="submit">Lưu</button>
        </form>

        <h3>Bảng điểm</h3>
        <table>
            <thead>
                <tr>
                    <th>Môn</th>
                    <th>Điểm</th>
                    <th>Ngày nhập</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo $row['mon_hoc']; ?></strong></td>
                    <td><?php echo $row['diem_so']; ?></td>
                    <td><small><?php echo date("d/m/Y", strtotime($row['ngay_nhap'])); ?></small></td>
                    <td>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" 
                        class="btn-delete" 
                        onclick="return confirm('Bạn chắc chắn muốn xóa môn này?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>