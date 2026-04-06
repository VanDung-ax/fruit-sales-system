<?php
class Product
{
    private $conn;
    private $table = "products";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        return $this->conn->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($name, $price, $image, $stock, $category)
    {
        $sql = "INSERT INTO " . $this->table . " (name, price, image, stock, category) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdsis", $name, $price, $image, $stock, $category);
        return $stmt->execute();
    }

    public function update($id, $name, $price, $image, $stock, $category)
    {
        if ($image) {
            // Có ảnh mới thì cập nhật cả cột image
            $sql = "UPDATE " . $this->table . " SET name=?, price=?, image=?, stock=?, category=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sdsisi", $name, $price, $image, $stock, $category, $id);
        } else {
            // Không có ảnh mới thì giữ nguyên ảnh cũ
            $sql = "UPDATE " . $this->table . " SET name=?, price=?, stock=?, category=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sdisi", $name, $price, $stock, $category, $id);
        }
        return $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
