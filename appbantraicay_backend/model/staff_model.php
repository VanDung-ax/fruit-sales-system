<?php
class Staff
{
    private $conn;
    private $table = "staffs";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY id_staff DESC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_staff = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $email, $phone, $role)
    {
        $sql = "INSERT INTO " . $this->table . " (full_name, email, phone, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $phone, $role);
        return $stmt->execute();
    }

    public function update($id, $name, $email, $phone, $role, $status)
    {
        $sql = "UPDATE " . $this->table . " SET full_name=?, email=?, phone=?, role=?, status=? WHERE id_staff=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $phone, $role, $status, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id_staff = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
