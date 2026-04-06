<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

$category_name = $_GET['category'] ?? '';

if (empty($category_name)) {
    echo json_encode([]);
    exit;
}

// Lọc sản phẩm theo cột 'category' trong bảng products
$sql = "SELECT * FROM products WHERE category = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category_name);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $row['id'] = (int)$row['id'];
    $row['price'] = (float)$row['price'];
    $row['stock'] = (int)$row['stock'];
    $products[] = $row;
}

echo json_encode($products);
