<?php
include_once __DIR__ . "/../model/payment_model.php";
include_once __DIR__ . "/../model/order_model.php";

class PaymentController
{
    private $payment;
    private $order;

    public function __construct($db)
    {
        $this->payment = new Payment($db);
        $this->order = new Order($db);
    }

    public function handleProcessCheckout()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        // 1. Tạo đơn hàng trước để lấy ID
        //$id_order = $this->order->createOrderShort($data['customer_name'], $data['product_name']);

        if ($id_order) {
            // 2. Lưu thông tin thanh toán riêng biệt
            if ($this->payment->createPayment($id_order, $data['phone'], $data['address'], $data['payment_method'])) {
                echo json_encode(["status" => "success", "message" => "Đặt hàng và lưu thanh toán thành công!"]);
            }
        }
    }
}
