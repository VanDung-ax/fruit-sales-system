<?php
include_once __DIR__ . "/../model/user_model.php";

class UserController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    public function handleAddUser($postData)
    {
        return $this->user->create($postData['name'], $postData['email'], $postData['password'], $postData['role'])
            ? ["status" => "success", "message" => "Thêm tài khoản thành công!"]
            : ["status" => "error", "message" => "Lỗi khi thêm tài khoản."];
    }

    public function handleUpdateUser($postData)
    {
        $password = !empty($postData['password']) ? $postData['password'] : null;
        return $this->user->update($postData['id'], $postData['name'], $postData['email'], $postData['role'], $password)
            ? ["status" => "success", "message" => "Cập nhật tài khoản thành công!"]
            : ["status" => "error", "message" => "Lỗi cập nhật."];
    }

    public function listUsers()
    {
        return $this->user->getAll();
    }
    public function getUserById($id)
    {
        return $this->user->getById($id);
    }
    public function deleteUser($id)
    {
        return $this->user->delete($id);
    }
}
