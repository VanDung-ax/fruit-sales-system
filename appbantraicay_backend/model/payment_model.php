<?php
class Payment
{
    private $conn;
    private $table = "payment_info";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createPayment($id_order, $phone, $address, $method)
    {
        $sql = "INSERT INTO " . $this->table . " (id_order, phone, address, payment_method) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $id_order, $phone, $address, $method);
        return $stmt->execute();
    }
}
