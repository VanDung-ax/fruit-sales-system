<?php
header("Content-Type: application/json");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);
$cart_id = $data['cart_id'];
$action = $data['action']; // 'increase', 'decrease', 'delete'

if ($action == 'increase') {
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
} else if ($action == 'decrease') {
    // Giảm nhưng không để dưới 1
    $stmt = $conn->prepare("UPDATE cart SET quantity = GREATEST(quantity - 1, 1) WHERE id = ?");
} else if ($action == 'delete') {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
}

$stmt->bind_param("i", $cart_id);
echo json_encode(["status" => $stmt->execute() ? "success" : "error"]);
