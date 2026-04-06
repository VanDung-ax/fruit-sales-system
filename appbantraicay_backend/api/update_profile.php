<?php
// Tên file: update_profile.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);
$user_id = (int)$data['user_id'];
$name = $data['name'];
$email = $data['email'];

if ($user_id > 0) {
    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Cập nhật thành công"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi cập nhật"]);
    }
}
