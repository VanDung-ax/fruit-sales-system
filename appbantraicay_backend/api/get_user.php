<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");
include("../controller/user_controller.php");

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// lấy tên theo id đã đăng nhập 
if ($user_id > 0) {
    $controller = new UserController($conn);
    $userData = $controller->getUserById($user_id); // Sử dụng hàm đã có trong user_controller.php

    if ($userData) {
        // Không trả về password để bảo mật
        unset($userData['password']);
        echo json_encode(["status" => "success", "data" => $userData]);
    } else {
        echo json_encode(["status" => "error", "message" => "Không tìm thấy người dùng"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID không hợp lệ"]);
}
