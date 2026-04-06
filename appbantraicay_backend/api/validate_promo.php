<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

$code = isset($_GET['code']) ? $_GET['code'] : '';

if (empty($code)) {
    echo json_encode(["status" => "error", "message" => "Vui lòng nhập mã"]);
    exit;
}
// Kiểm tra mã: Trạng thái hoạt động, số lượng còn lại > 0, và chưa hết hạn
$sql = "SELECT discount_value, discount_type FROM promotions 
        WHERE code = ? AND status = 1 AND quantity > 0 AND expiry_date >= CURDATE() 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "status" => "success",
        "discount_value" => (float)$row['discount_value'], // QUAN TRỌNG: Ép kiểu float ở đây
        "discount_type" => $row['discount_type']           // 'percentage' hoặc 'fixed'
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Mã không hợp lệ hoặc đã hết hạn"]);
}
