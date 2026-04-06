<?php
include_once __DIR__ . "/../model/products_model.php";

class ProductController
{
    private $product;

    public function __construct($db)
    {
        $this->product = new Product($db);
    }

    public function handleAddProduct($postData, $fileData)
    {
        $name = $postData['name'] ?? '';
        $price = $postData['price'] ?? 0;
        $stock = $postData['stock'] ?? 0;
        $category = $postData['category'] ?? '';

        $targetDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $imageName = time() . '_' . basename($fileData['image']['name']);
        $targetPath = $targetDir . $imageName;

        if (move_uploaded_file($fileData['image']['tmp_name'], $targetPath)) {
            if ($this->product->create($name, $price, $imageName, $stock, $category)) {
                return ["status" => "success", "message" => "Thêm sản phẩm thành công!"];
            }
        }
        return ["status" => "error", "message" => "Lỗi xử lý file hoặc dữ liệu."];
    }

    public function getProductById($id)
    {
        return $this->product->getById($id);
    }

    public function handleUpdateProduct($postData, $fileData)
    {
        $id = $postData['id'];
        $name = $postData['name'] ?? '';
        $price = $postData['price'] ?? 0;
        $stock = $postData['stock'] ?? 0;
        $category = $postData['category'] ?? '';
        $imageName = null;

        if (isset($fileData['image']) && $fileData['image']['error'] === 0) {
            $targetDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR;
            $imageName = time() . '_' . basename($fileData['image']['name']);
            move_uploaded_file($fileData['image']['tmp_name'], $targetDir . $imageName);
        }

        if ($this->product->update($id, $name, $price, $imageName, $stock, $category)) {
            return ["status" => "success", "message" => "Cập nhật thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi khi cập nhật."];
    }

    public function listProducts()
    {
        return $this->product->getAll();
    }
    public function deleteProduct($id)
    {
        return $this->product->delete($id);
    }
}
