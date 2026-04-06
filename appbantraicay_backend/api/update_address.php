<?php
// File: update_address.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id']) && isset($data['address'])) {
    $user_id = (int)$data['user_id'];
    $full_name = $data['full_name'];
    $phone = $data['phone'];
    $address = $data['address'];

    // Cập nhật thông tin vào đơn hàng mới nhất của người dùng này trong bảng orders_dh
    // Sử dụng ORDER BY id DESC LIMIT 1 để chỉ tác động vào đơn hàng gần đây nhất
    $sql = "UPDATE orders_dh 
            SET full_name = ?, phone = ?, address = ? 
            WHERE user_id = ? 
            ORDER BY id DESC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Đã cập nhật địa chỉ thành công"]);
        } else {
            echo json_encode(["status" => "empty", "message" => "Không tìm thấy đơn hàng để cập nhật"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi truy vấn database"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu đầu vào"]);
}
$conn->close();
