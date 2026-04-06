<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Kết nối database
include("../config/database.php");

// Nhận dữ liệu từ Flutter
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin"]);
    exit;
}

// 1. Kiểm tra xem Email đã tồn tại chưa
$checkSql = "SELECT id FROM users WHERE email = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email này đã được sử dụng"]);
} else {
    // 2. MÃ HÓA MẬT KHẨU (Quan trọng để đồng bộ với User Model)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'customer';

    $insertSql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if ($insertStmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Đăng ký thành công"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi hệ thống không thể đăng ký"]);
    }
}
