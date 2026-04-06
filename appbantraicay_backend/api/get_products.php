<?php
// Tên file: get_products.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

// Lấy danh sách sản phẩm mới nhất
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    // Ép kiểu dữ liệu để Flutter không bị lỗi định dạng
    $row['id'] = (int)$row['id'];
    $row['price'] = (float)$row['price'];
    $row['stock'] = (int)$row['stock'];
    $products[] = $row;
}

echo json_encode($products);
