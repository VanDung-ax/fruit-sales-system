<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include("../config/database.php");

// Lấy danh sách danh mục từ table categories
$sql = "SELECT * FROM categories ORDER BY id_category DESC";
$result = $conn->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = [
        "id" => $row['id_category'],
        "name" => $row['name'],
        "description" => $row['description']
    ];
}

echo json_encode($categories);
