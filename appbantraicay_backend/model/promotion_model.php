<?php
class Promotion
{
    private $conn;
    private $table = "promotions";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        // Tự động xác định trạng thái dựa trên thời gian và số lượng
        $sql = "SELECT *, 
                CASE 
                    WHEN status = 0 THEN 'Tạm khóa'
                    WHEN expiry_date < CURDATE() THEN 'Hết hạn'
                    WHEN quantity > 0 AND used_count >= quantity THEN 'Hết lượt'
                    ELSE 'Đang chạy'
                END as status_text
                FROM " . $this->table . " ORDER BY id_promotion DESC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_promotion = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($code, $value, $type, $quantity, $expiry, $desc)
    {
        $sql = "INSERT INTO " . $this->table . " (code, discount_value, discount_type, quantity, expiry_date, description, used_count, status) VALUES (?, ?, ?, ?, ?, ?, 0, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdsiss", $code, $value, $type, $quantity, $expiry, $desc);
        return $stmt->execute();
    }

    public function update($id, $code, $value, $type, $quantity, $expiry, $desc, $status)
    {
        $sql = "UPDATE " . $this->table . " SET code = ?, discount_value = ?, discount_type = ?, quantity = ?, expiry_date = ?, description = ?, status = ? WHERE id_promotion = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdsissii", $code, $value, $type, $quantity, $expiry, $desc, $status, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id_promotion = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
