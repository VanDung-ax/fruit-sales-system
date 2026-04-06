<?php
// Tên file: search_products.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

$keyword = $_GET['keyword'] ?? '';

if (empty($keyword)) {
    echo json_encode([]);
    exit;
}

// Tìm kiếm theo tên sản phẩm HOẶC tên danh mục (Sử dụng LIKE để tìm kiếm tương đối)
$searchQuery = "%$keyword%";
$sql = "SELECT * FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchQuery, $searchQuery);
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
