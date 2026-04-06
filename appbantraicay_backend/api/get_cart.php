<?php
// 1. Cấu hình Header để hỗ trợ Ngrok và Flutter
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");
header("Content-Type: application/json; charset=UTF-8");

// Xử lý request OPTIONS (Preflight) của trình duyệt/ngrok
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
}

include("../config/database.php");

// 2. Lấy user_id từ tham số URL
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id <= 0) {
        echo json_encode([]);
        exit;
}

// 3. Truy vấn lấy thông tin giỏ hàng kèm thông tin sản phẩm
$sql = "SELECT 
            c.id as cart_id, 
            p.id as product_id,
            p.name, 
            p.price, 
            p.image, 
            c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? 
        ORDER BY c.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];

// 4. Lặp qua kết quả và ép kiểu dữ liệu chuẩn cho Flutter
while ($row = $result->fetch_assoc()) {
        $row['cart_id'] = (int)$row['cart_id'];
        $row['product_id'] = (int)$row['product_id'];
        $row['price'] = (float)$row['price'];
        $row['quantity'] = (int)$row['quantity'];

        $cart_items[] = $row;
}

// 5. Trả về kết quả JSON
echo json_encode($cart_items);
