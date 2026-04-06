<?php
// Tên file: clear_cart.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);
$user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;

if ($user_id > 0) {
    // Xóa tất cả sản phẩm trong giỏ hàng của user này
    $sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Đã dọn dẹp giỏ hàng"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi thực thi SQL"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu user_id"]);
}
