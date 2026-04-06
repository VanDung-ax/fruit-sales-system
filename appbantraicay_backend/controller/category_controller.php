<?php
include_once __DIR__ . "/../model/category_model.php";

class CategoryController
{
    private $category;

    public function __construct($db)
    {
        $this->category = new Category($db);
    }

    public function getCategoryById($id)
    {
        return $this->category->getById($id);
    }

    public function handleAddCategory($postData)
    {
        $name = $postData['name'] ?? '';
        $description = $postData['description'] ?? '';
        if ($this->category->create($name, $description)) {
            return ["status" => "success", "message" => "Thêm danh mục thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi khi thêm danh mục."];
    }

    public function handleUpdateCategory($postData)
    {
        $id = $postData['id_category'];
        $name = $postData['name'] ?? '';
        $description = $postData['description'] ?? '';
        if ($this->category->update($id, $name, $description)) {
            return ["status" => "success", "message" => "Cập nhật danh mục thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi khi cập nhật."];
    }

    public function listCategories()
    {
        return $this->category->getAll();
    }

    public function deleteCategory($id)
    {
        return $this->category->delete($id);
    }
}
