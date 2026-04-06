<?php
include_once __DIR__ . "/../model/oder_model.php";

class OrderController
{
    private $order;

    public function __construct($db)
    {
        $this->order = new Order($db);
    }

    // Trả về danh sách đơn hàng cho View
    public function listOrders($search_id = null)
    {
        return $this->order->getAll($search_id);
    }

    // Xử lý cập nhật trạng thái đơn hàng
    public function handleUpdateStatus($postData)
    {
        $id = (int)$postData['order_id'];
        $status = $postData['status'];

        if ($this->order->updateStatus($id, $status)) {
            return ["status" => "success", "message" => "Cập nhật trạng thái đơn hàng #$id thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi: Không thể cập nhật trạng thái."];
    }

    // Xử lý xóa đơn hàng
    public function handleDelete($id)
    {
        if ($this->order->delete($id)) {
            return ["status" => "success", "message" => "Đã xóa đơn hàng thành công."];
        }
        return ["status" => "error", "message" => "Lỗi khi xóa đơn hàng."];
    }
}
