<?php
include_once __DIR__ . "/../model/staff_model.php";

class StaffController
{
    private $staff;

    public function __construct($db)
    {
        $this->staff = new Staff($db);
    }

    // --- BỔ SUNG HÀM NÀY ---
    public function getById($id)
    {
        return $this->staff->getById($id);
    }
    // -----------------------

    public function listStaffs()
    {
        return $this->staff->getAll();
    }

    public function handleAddStaff($postData)
    {
        $name = $postData['full_name'] ?? '';
        $email = $postData['email'] ?? '';
        $phone = $postData['phone'] ?? '';
        $role = $postData['role'] ?? 'staff';

        if ($this->staff->create($name, $email, $phone, $role)) {
            return ["status" => "success", "message" => "Thêm nhân viên thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi: Email có thể đã tồn tại."];
    }

    public function handleUpdateStaff($postData)
    {
        $id = $postData['id_staff'];
        $name = $postData['full_name'];
        $email = $postData['email'];
        $phone = $postData['phone'];
        $role = $postData['role'];
        $status = $postData['status'];

        if ($this->staff->update($id, $name, $email, $phone, $role, $status)) {
            return ["status" => "success", "message" => "Cập nhật nhân viên thành công!"];
        }
        return ["status" => "error", "message" => "Lỗi cập nhật."];
    }

    public function deleteStaff($id)
    {
        return $this->staff->delete($id);
    }
}
