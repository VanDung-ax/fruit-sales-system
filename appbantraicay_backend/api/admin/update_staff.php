<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/staff_controller.php';

$controller = new StaffController($conn);
$message = $_GET['msg'] ?? "";
$staff = null;

// Lấy thông tin nhân viên để đổ vào form
if (isset($_GET['id'])) {
    $staff = $controller->getById($_GET['id']);
}

// Xử lý khi nhấn nút UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_staff'])) {
    $result = $controller->handleUpdateStaff($_POST);
    if ($result['status'] === 'success') {
        header("Location: staffs.php?msg=" . urlencode($result['message']));
        exit();
    } else {
        $message = $result['message'];
        $staff = $controller->getById($_POST['id_staff']); // Lấy lại dữ liệu nếu lỗi
    }
}

if (!$staff) {
    echo "Nhân viên không tồn tại!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Staff - <?= htmlspecialchars($staff['full_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #060d06;
            color: #e2e8f0;
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

        .sidebar-item-active {
            background-color: #0f1f0f;
            border-left: 4px solid #13ec13;
            color: #13ec13;
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
            <a href="staffs.php" class="block sidebar-item-active p-3 rounded-lg font-semibold transition">Staffs</a>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Chỉnh sửa nhân sự #<?= $staff['id_staff'] ?></h1>
                <a href="staffs.php" class="text-gray-500 hover:text-white transition">← Quay lại danh sách</a>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/20 text-red-500 border border-red-900"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <input type="hidden" name="id_staff" value="<?= $staff['id_staff'] ?>">

                <div class="card-bg p-8 rounded-2xl space-y-6">
                    <h2 class="text-lg font-semibold text-[#13ec13] border-b border-white/10 pb-2">Thông tin cá nhân</h2>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Họ và tên</label>
                        <input type="text" name="full_name" class="input-dark" value="<?= htmlspecialchars($staff['full_name']) ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" class="input-dark" value="<?= htmlspecialchars($staff['email']) ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Số điện thoại</label>
                        <input type="text" name="phone" class="input-dark" value="<?= htmlspecialchars($staff['phone']) ?>">
                    </div>
                </div>

                <div class="card-bg p-8 rounded-2xl space-y-6">
                    <h2 class="text-lg font-semibold text-[#13ec13] border-b border-white/10 pb-2">Quản trị nội bộ</h2>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Cấp bậc (Role)</label>
                        <select name="role" class="input-dark">
                            <option value="staff" <?= $staff['role'] == 'staff' ? 'selected' : '' ?>>Nhân viên (Staff)</option>
                            <option value="manager" <?= $staff['role'] == 'manager' ? 'selected' : '' ?>>Quản lý (Manager)</option>
                            <option value="admin" <?= $staff['role'] == 'admin' ? 'selected' : '' ?>>Quản trị viên (Admin)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Trạng thái làm việc</label>
                        <select name="status" class="input-dark <?= $staff['status'] == 'active' ? 'text-green-500' : 'text-red-500' ?>">
                            <option value="active" <?= $staff['status'] == 'active' ? 'selected' : '' ?>>Đang làm (Active)</option>
                            <option value="inactive" <?= $staff['status'] == 'inactive' ? 'selected' : '' ?>>Đã nghỉ việc (Inactive)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-2 italic">* Nếu chuyển sang "Đã nghỉ việc", nhân viên sẽ không thể đăng nhập vào hệ thống.</p>
                    </div>
                    <div class="pt-4">
                        <button type="submit" name="update_staff" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition">
                            LƯU THAY ĐỔI
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>

</html>