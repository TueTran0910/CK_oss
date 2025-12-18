<?php
include 'db.php';

// --- XỬ LÝ PHP (THÊM HOẶC SỬA) ---
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mon = trim($_POST['mon_hoc']);
    $diem = $_POST['diem_so'];
    // Nếu có ID thì là SỬA, không có thì là THÊM
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; 

    if(!empty($mon) && is_numeric($diem)){
        if ($id > 0) {
            // Logic SỬA (Update)
            $stmt = $conn->prepare("UPDATE diem_thi SET mon_hoc=?, diem_so=? WHERE id=?");
            $stmt->bind_param("sdi", $mon, $diem, $id);
        } else {
            // Logic THÊM (Insert)
            $stmt = $conn->prepare("INSERT INTO diem_thi (mon_hoc, diem_so) VALUES (?, ?)");
            $stmt->bind_param("sd", $mon, $diem);
        }

        if($stmt->execute()){
            $stmt->close();
            $conn->close();
            header("Location: index.php"); // Load lại trang để xóa dữ liệu form
            exit();
        } else {
            $msg = "<div class='message error'>❌ Lỗi: " . $conn->error . "</div>";
        }
    }
}

// LẤY DANH SÁCH
$result = $conn->query("SELECT * FROM diem_thi ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Điểm Thi</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="container">
        <div class="profile-info">
            <h1>Hồ Sơ Sinh Viên.</h1>
            <p>Tên: <strong>Trần Thiên Tuệ.</strong> - MSSV: <strong>DH52201727.</strong></p>
            <p>Lớp: <strong>D22_TH10.</strong></p>
            <p>Trường: <strong>Đại Học Công Nghệ Sài Gòn (STU).</strong></p>
        </div>

        <?php echo $msg; ?>

        <form method="POST" class="input-group">
            <input type="text" name="mon_hoc" placeholder="Nhập môn học mới..." required>
            <input type="number" name="diem_so" step="0.1" min="0" max="10" placeholder="Điểm" required>
            <input type="hidden" name="id" value="0"> 
            <button type="submit" class="btn-add">➕ Thêm Ngay</button>
        </form>

        <h3>📋 Bảng điểm chi tiết</h3>
        <table>
            <thead>
                <tr>
                    <th>Môn Học</th>
                    <th>Điểm</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight:600"><?php echo htmlspecialchars($row['mon_hoc']); ?></td>
                    <td>
                        <?php 
                            $d = $row['diem_so'];
                            $class = ($d >= 5) ? 'score-badge' : 'score-badge fail';
                            echo "<span class='$class'>$d</span>";
                        ?>
                    </td>
                    <td>
                        <button class="btn-edit" onclick="openModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['mon_hoc']); ?>', <?php echo $row['diem_so']; ?>)">
                            ✏️ Sửa
                        </button>

                        <a href="delete.php?id=<?php echo $row['id']; ?>" 
                           class="btn-delete"
                           onclick="return confirm('Xóa môn này nhé?')">🗑️ Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2 style="color:#4a00e0; text-align:center;">✏️ Cập Nhật Điểm</h2>
            
            <form method="POST" style="display:flex; flex-direction:column; gap:15px;">
                <input type="hidden" id="edit_id" name="id">
                
                <div>
                    <label style="font-weight:bold; color:#555;">Môn học:</label>
                    <input type="text" id="edit_mon" name="mon_hoc" required style="width:100%;">
                </div>
                
                <div>
                    <label style="font-weight:bold; color:#555;">Điểm số:</label>
                    <input type="number" id="edit_diem" name="diem_so" step="0.1" min="0" max="10" required style="width:100%;">
                </div>

                <button type="submit" class="btn-add" style="width:100%;">Lưu Thay Đổi</button>
            </form>
        </div>
    </div>

    <script>
        // Hàm mở Popup và điền dữ liệu cũ vào
        function openModal(id, mon, diem) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_mon').value = mon;
            document.getElementById('edit_diem').value = diem;
            document.getElementById('editModal').style.display = "block";
        }

        // Hàm đóng Popup
        function closeModal() {
            document.getElementById('editModal').style.display = "none";
        }

        // Bấm ra ngoài vùng trắng thì tự đóng
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }
    </script>

</body>
</html>