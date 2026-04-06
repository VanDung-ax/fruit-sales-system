<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/staff_controller.php';

$controller = new StaffController($conn);
$message = $_GET['msg'] ?? "";

// Xử lý xóa
if (isset($_GET['delete_id'])) {
    if ($controller->deleteStaff($_GET['delete_id'])) {
        header("Location: staffs.php?msg=" . urlencode("Xóa nhân viên thành công!"));
        exit();
    }
}

// Xử lý thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handleAddStaff($_POST);
    if ($result['status'] === 'success') {
        header("Location: staffs.php?msg=" . urlencode($result['message']));
        exit();
    }
    $message = $result['message'];
}

$staffs = $controller->listStaffs();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin - Staff Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #060d06;
            color: #e2e8f0;
        }

        .sidebar-item-active {
            background-color: #0f1f0f;
            border-left: 4px solid #13ec13;
            color: #13ec13;
        }

        .card-bg {
            background-color: #0b140b;
            border: 1px solid #1a2e1a;
        }

        .input-dark {
            background-color: #112211;
            border: 1px solid #1a2e1a;
            color: white;
            border-radius: 12px;
            padding: 12px;
            width: 100%;
        }

        #toast-message {
            transition: opacity 0.5s ease;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden">
    <aside class="w-64 card-bg flex flex-col p-6">
        <div class="flex items-center gap-2 mb-10">
            <div class="bg-[#13ec13] p-1.5 rounded-lg text-black font-bold">F</div>
            <span class="text-xl font-bold text-white">Fruitly Admin</span>
        </div>
        <nav class="flex-1 space-y-2">
            <a href="dashboard.php" class="block p-3 text-gray-500 hover:text-white transition">Dashboard</a>
            <a href="add_products.php" class="block p-3 text-gray-500 hover:text-white transition">Quản lý sản phẩm</a>
            <a href="categories.php" class="block p-3 text-gray-500 hover:text-white transition">Quản lý danh mục</a>
            <a href="users.php" class="block p-3 text-gray-500 hover:text-white transition">Quản lý người dùng</a>
            <a href="promotions.php" class="block p-3 text-gray-500 hover:text-white transition">Quản lý khuyến mãi</a>
            <a href="orders.php" class="block p-3 text-gray-500 hover:text-white transition">Quản lý đơn hàng</a>
            <a href="staffs.php" class="block sidebar-item-active p-3 rounded-lg font-semibold transition">Quản lý nhân viên</a>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Thêm nhân viên</h1>
            <div class="text-sm text-gray-500">Quản lý đội ngũ nhân sự</div>
        </div>

        <?php if ($message): ?>
            <div id="toast-message" class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="md:col-span-3 card-bg p-8 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="text" name="full_name" class="input-dark" placeholder="Họ và tên nhân viên" required>
                <input type="email" name="email" class="input-dark" placeholder="Email (dùng để đăng nhập)" required>
                <input type="text" name="phone" class="input-dark" placeholder="Số điện thoại">
                <select name="role" class="input-dark">
                    <option value="staff">Nhân viên (Staff)</option>
                    <option value="manager">Quản lý (Manager)</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                </select>
            </div>
            <div class="card-bg p-8 rounded-2xl flex items-end">
                <button type="submit" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition">
                    Thêm nhân viên
                </button>
            </div>
        </form>

        <div class="card-bg rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#0f1f0f] text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">Họ tên & Liên hệ</th>
                        <th class="p-4">Chức vụ</th>
                        <th class="p-4">Trạng thái</th>
                        <th class="p-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a2e1a]">
                    <?php while ($row = $staffs->fetch_assoc()): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4">
                                <div class="font-bold text-white"><?= $row['full_name'] ?></div>
                                <div class="text-xs text-gray-500"><?= $row['email'] ?> | <?= $row['phone'] ?></div>
                            </td>
                            <td class="p-4">
                                <?php
                                $roleColor = "text-gray-400 bg-gray-900";
                                if ($row['role'] == 'admin') $roleColor = "text-purple-400 bg-purple-900/20";
                                if ($row['role'] == 'manager') $roleColor = "text-blue-400 bg-blue-900/20";
                                ?>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold <?= $roleColor ?>">
                                    <?= strtoupper($row['role']) ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="flex items-center gap-2 <?= $row['status'] == 'active' ? 'text-green-500' : 'text-red-500' ?> font-semibold">
                                    <span class="w-2 h-2 rounded-full <?= $row['status'] == 'active' ? 'bg-green-500' : 'bg-red-500' ?>"></span>
                                    <?= $row['status'] == 'active' ? 'Đang làm' : 'Nghỉ làm' ?>
                                </span>
                            </td>
                            <td class="p-4 text-center space-x-4">
                                <a href="update_staff.php?id=<?= $row['id_staff'] ?>" class="text-blue-500 hover:text-blue-400 font-semibold">Edit</a>
                                <a href="?delete_id=<?= $row['id_staff'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?')" class="text-red-500 hover:text-red-400 font-semibold">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Tự động ẩn thông báo sau 3 giây
        const msg = document.getElementById('toast-message');
        if (msg) {
            setTimeout(() => {
                msg.style.opacity = '0';
                setTimeout(() => {
                    msg.remove();
                    const url = new URL(window.location);
                    url.searchParams.delete('msg');
                    window.history.replaceState({}, document.title, url);
                }, 500);
            }, 3000);
        }
    </script>
</body>

</html>