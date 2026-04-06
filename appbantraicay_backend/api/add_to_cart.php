<?php
header("Content-Type: application/json");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'];
$product_id = $data['product_id'];

$check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $new_qty = $row['quantity'] + 1;
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_qty, $row['id']);
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $user_id, $product_id);
}

echo json_encode(["status" => $stmt->execute() ? "success" : "error"]);
