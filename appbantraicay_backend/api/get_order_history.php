<?php
// Tên file: get_order_history.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include("../config/database.php");

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id > 0) {
    // CẬP NHẬT: Thêm o.payment_status vào câu lệnh SELECT
    $sql = "SELECT 
                o.id, 
                o.total_amount, 
                o.payment_method, 
                o.payment_status, 
                o.created_at, 
                o.promotion_code,
                GROUP_CONCAT(
                    JSON_OBJECT(
                        'name', p.name,
                        'image', p.image,
                        'quantity', od.quantity,
                        'price', od.price
                    )
                ) as items
            FROM orders_dh o
            JOIN order_details od ON o.id = od.order_id
            JOIN products p ON od.product_id = p.id
            WHERE o.user_id = ? 
            GROUP BY o.id
            ORDER BY o.id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    $total_spent = 0;

    while ($row = $result->fetch_assoc()) {
        $row['id'] = (int)$row['id'];
        $row['total_amount'] = (float)$row['total_amount'];
        // Chuyển chuỗi JSON items thành mảng PHP
        $row['items'] = json_decode("[" . $row['items'] . "]", true);

        // Đảm bảo payment_status có giá trị mặc định nếu null
        $row['payment_status'] = $row['payment_status'] ?? "Chưa thanh toán";

        $orders[] = $row;

        // Cộng dồn tổng chi tiêu để tính hạng thành viên
        $total_spent += $row['total_amount'];
    }

    // Logic phân hạng thành viên
    $rank_name = "Đồng";
    if ($total_spent >= 10000000) $rank_name = "Kim Cương";
    else if ($total_spent >= 5000000) $rank_name = "Vàng";
    else if ($total_spent >= 1000000) $rank_name = "Bạc";

    echo json_encode([
        "status" => "success",
        "orders" => $orders,
        "stats" => [
            "total_orders" => count($orders),
            "total_spent" => $total_spent,
            "rank_name" => $rank_name
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu user_id"]);
}
$conn->close();
