<?php
include_once __DIR__ . "/../model/promotion_model.php";

class PromotionController
{
    private $promotion;

    public function __construct($db)
    {
        $this->promotion = new Promotion($db);
    }

    public function listPromotions()
    {
        return $this->promotion->getAll();
    }

    public function handleAdd($postData)
    {
        $code = $postData['code'] ?? '';
        $value = $postData['discount_value'] ?? 0;
        $type = $postData['discount_type'] ?? 'percentage';
        $quantity = $postData['quantity'] ?? 0;
        $expiry = $postData['expiry_date'] ?? '';
        $desc = $postData['description'] ?? '';

        if ($this->promotion->create($code, $value, $type, $quantity, $expiry, $desc)) {
            return ["status" => "success", "message" => "Thêm mã giảm giá thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi: Có thể mã đã tồn tại hoặc dữ liệu sai."];
    }

    public function handleUpdate($postData)
    {
        $id = $postData['id_promotion'];
        $code = $postData['code'];
        $value = $postData['discount_value'];
        $type = $postData['discount_type'];
        $quantity = $postData['quantity'];
        $expiry = $postData['expiry_date'];
        $desc = $postData['description'];
        $status = $postData['status'];

        if ($this->promotion->update($id, $code, $value, $type, $quantity, $expiry, $desc, $status)) {
            return ["status" => "success", "message" => "Cập nhật thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi khi cập nhật."];
    }

    public function delete($id)
    {
        return $this->promotion->delete($id);
    }
}
