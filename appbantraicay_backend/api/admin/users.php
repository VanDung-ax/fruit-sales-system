<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/user_controller.php';

$controller = new UserController($conn);
$message = isset($_GET['msg']) ? $_GET['msg'] : "";

// Xử lý Xóa tài khoản
if (isset($_GET['delete_id'])) {
    if ($controller->deleteUser($_GET['delete_id'])) {
        header("Location: users.php?msg=" . urlencode("Xóa tài khoản thành công!"));
        exit();
    }
}

// Xử lý Thêm tài khoản mới
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handleAddUser($_POST);
    if ($result['status'] === 'success') {
        header("Location: users.php?msg=" . urlencode($result['message']));
        exit();
    } else {
        $message = $result['message'];
    }
}

$users = $controller->listUsers();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin - User Management</title>
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

        .input-dark:focus {
            border-color: #13ec13;
            outline: none;
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
            <span class="text-xl font-bold text-white">Fruitly <span class="text-gray-500 font-light text-sm">Admin</span></span>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all">
                <a href="dashboard.php" class="flex items-center gap-3 w-full">Dashboard</a>
            </div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all">
                <a href="add_products.php" class="flex items-center gap-3 w-full">Quản lý sản phẩm</a>
            </div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all">
                <a href="categories.php" class="flex items-center gap-3 w-full">Quản lý danh mục</a>
            </div>
            <div class="sidebar-item-active p-3 rounded-lg flex items-center gap-3 font-semibold">
                <a href="users.php" class="w-full">Quản lý người dùng</a>
            </div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all">
                <a href="promotions.php" class="flex items-center gap-3 w-full">Quản lý khuyến mãi</a>

            </div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all">
                <a href="orders.php" class="flex items-center gap-3 w-full">Quản lý đơn hàng</a>
            </div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all">
                <a href="staffs.php" class="flex items-center gap-3 w-full">Quản lý nhân viên</a>
            </div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-8">Thêm người dùng</h1>

        <?php if ($message): ?>
            <div id="toast-message" class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div class="card-bg p-8 rounded-2xl space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Tên người dùng</label>
                    <input type="text" name="name" class="input-dark" placeholder="John Doe" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Email Address</label>
                    <input type="email" name="email" class="input-dark" placeholder="example@mail.com" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Password</label>
                        <input type="password" name="password" class="input-dark" placeholder="••••••••" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Role</label>
                        <select name="role" class="input-dark">
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-bg p-8 rounded-2xl flex flex-col justify-end">
                <p class="text-sm text-gray-500 mb-6 italic">Lưu ý: Tài khoản Admin có quyền quản lý toàn bộ hệ thống. Hãy cẩn thận khi cấp quyền.</p>
                <button type="submit" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition">Thêm người dùng</button>
            </div>
        </form>

        <div class="card-bg rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800">
                <h4 class="font-bold text-white">Danh sách người dùng</h4>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="text-gray-500 text-xs uppercase bg-[#0f1f0f]/30">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <tr class="border-b border-gray-800/50 hover:bg-white/5 transition">
                            <td class="p-4 font-mono text-gray-500">#<?= $row['id'] ?></td>
                            <td class="p-4 font-bold text-white"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="p-4"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold <?= $row['role'] == 'admin' ? 'bg-red-500/10 text-red-500' : 'bg-green-500/10 text-green-500' ?>">
                                    <?= strtoupper($row['role']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center space-x-3">
                                <a href="update_user.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-400 transition font-semibold">Edit</a>
                                <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')" class="text-red-500 hover:text-red-400 transition font-semibold">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Tự ẩn thông báo sau 2s và dọn sạch URL
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
            }, 2000);
        }
    </script>
</body>

</html>