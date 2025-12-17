<?php
include 'db.php'; // File chứa cấu hình sql300.infinityfree.com

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM diem_thi WHERE id = $id";
    $conn->query($sql);
}

header("Location: index.php"); // Xóa xong tự động quay về trang chủ
exit();
?>