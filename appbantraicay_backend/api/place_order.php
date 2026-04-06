<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

include("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['user_id']) || !isset($data['items'])) {
    echo json_encode(["status" => "error", "message" => "Dữ liệu đầu vào không hợp lệ"]);
    exit;
}

$user_id = (int)$data['user_id'];
$full_name = $data['full_name'];
$phone = $data['phone'];
$address = $data['address'];
$total = (float)$data['total_amount'];
$payment = $data['payment_method'];
$promo_code = isset($data['promotion_code']) ? trim($data['promotion_code']) : null; // Mã khuyến mãi từ Flutter
$items = $data['items'];

$conn->begin_transaction();
$payment_status = isset($data['payment_status']) ? $data['payment_status'] : 'Chưa thanh toán';

try {
    // 1. Lưu đơn hàng chính vào bảng orders_dh
    $sql_order = "INSERT INTO orders_dh (user_id, full_name, phone, address, total_amount, payment_method,payment_status, promotion_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("isssdsss", $user_id, $full_name, $phone, $address, $total, $payment, $payment_status, $promo_code);

    if (!$stmt->execute()) {
        throw new Exception("Lỗi khi tạo đơn hàng chính");
    }

    $order_id = $conn->insert_id;

    // 2. Lưu chi tiết và trừ kho sản phẩm
    foreach ($items as $item) {
        $p_id = (int)$item['product_id'];
        $qty = (int)$item['quantity'];
        $price = (float)$item['price'];

        // Lưu vào order_details
        $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        $stmt_detail->bind_param("iiid", $order_id, $p_id, $qty, $price);
        $stmt_detail->execute();

        // Cập nhật kho sản phẩm (Đã chạy tốt)
        $sql_stock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("iii", $qty, $p_id, $qty);
        $stmt_stock->execute();

        if ($stmt_stock->affected_rows == 0) {
            throw new Exception("Sản phẩm ID $p_id không đủ hàng");
        }
    }

    // 3. CẬP NHẬT KHUYẾN MÃI (Sửa lỗi tại đây)
    if (!empty($promo_code)) {
        // Trừ 1 ở quantity và cộng 1 ở used_count
        $sql_promo = "UPDATE promotions 
                  SET quantity = quantity - 1, used_count = used_count + 1 
                  WHERE code = ? AND status = 1 AND quantity > 0";

        $stmt_promo = $conn->prepare($sql_promo);
        $stmt_promo->bind_param("s", $promo_code);
        $stmt_promo->execute();

        if ($stmt_promo->affected_rows == 0) {
            throw new Exception("Mã khuyến mãi '$promo_code' đã hết lượt sử dụng hoặc không tồn tại.");
        }
    }

    // 4. Xóa giỏ hàng
    $sql_clear = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear = $conn->prepare($sql_clear);
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Đặt hàng và cập nhật khuyến mãi thành công!"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
