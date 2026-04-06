<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id > 0) {
    // Lấy Tên, SĐT, Địa chỉ từ đơn hàng mới nhất trong bảng orders_dh
    $sql = "SELECT full_name, phone, address FROM orders_dh WHERE user_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["status" => "success", "data" => $row]);
    } else {
        echo json_encode(["status" => "empty", "message" => "Chưa có đơn hàng cũ"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu user_id"]);
}
