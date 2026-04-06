<?php
class Order
{
    private $conn;
    private $table = "orders_dh"; // Tên bảng chính xác từ database

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách đơn hàng (hỗ trợ tìm kiếm theo ID)
    public function getAll($search_id = null)
    {
        if ($search_id) {
            $sql = "SELECT * FROM " . $this->table . " WHERE id = ? ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $search_id);
            $stmt->execute();
            return $stmt->get_result();
        }

        $sql = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    // Lấy chi tiết sản phẩm trong một đơn hàng
    public function getOrderItems($order_id)
    {
        $sql = "SELECT od.*, p.name, p.image 
                FROM order_details od 
                JOIN products p ON od.product_id = p.id 
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Cập nhật trạng thái đơn hàng (Sử dụng enum: Đang chờ, Xác nhận,...)
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    // Xóa đơn hàng (và chi tiết đơn hàng)
    public function delete($id)
    {
        $this->conn->begin_transaction();
        try {
            // Xóa chi tiết trước
            $sql_details = "DELETE FROM order_details WHERE order_id = ?";
            $stmt1 = $this->conn->prepare($sql_details);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            // Xóa đơn hàng chính
            $sql_order = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt2 = $this->conn->prepare($sql_order);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
