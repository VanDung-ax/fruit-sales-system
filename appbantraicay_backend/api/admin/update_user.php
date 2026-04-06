<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/user_controller.php';

$controller = new UserController($conn);
$message = "";

if (isset($_GET['id'])) {
    $u = $controller->getUserById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $result = $controller->handleUpdateUser($_POST);
    if ($result['status'] === 'success') {
        header("Location: users.php?msg=" . urlencode($result['message']));
        exit();
    }
    $message = $result['message'];
}

if (!$u) {
    echo "Tài khoản không tồn tại!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User - <?= htmlspecialchars($u['name']) ?></title>
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
    </style>
</head>

<body class="flex h-screen overflow-hidden">
    <aside class="w-64 card-bg flex flex-col p-6">
        <div class="flex items-center gap-2 mb-10 text-[#13ec13] font-bold text-xl">
            <div class="bg-[#13ec13] p-1.5 rounded-lg text-black font-bold">F</div>
            <span class="text-xl font-bold text-white">Fruitly <span class="text-gray-500 font-light text-sm">Admin</span></span>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="dashboard.php">Dashboard</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="add_products.php">Product</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="categories.php">Categories</a></div>
            <div class="sidebar-item-active p-3 rounded-lg font-semibold"><a href="users.php">Users</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Edit User: #<?= $u['id'] ?></h1>
            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8 card-bg p-8 rounded-2xl">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <div class="space-y-4">
                    <input type="text" name="name" class="input-dark" value="<?= htmlspecialchars($u['name']) ?>" required>
                    <input type="email" name="email" class="input-dark" value="<?= htmlspecialchars($u['email']) ?>" required>
                    <select name="role" class="input-dark">
                        <option value="customer" <?= $u['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="space-y-4 flex flex-col justify-between">
                    <div>
                        <label class="block text-xs text-gray-500 mb-2 italic">Mật khẩu mới (để trống nếu không đổi)</label>
                        <input type="password" name="password" class="input-dark" placeholder="••••••••">
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" name="update_user" class="flex-1 bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition">UPDATE</button>
                        <a href="users.php" class="px-8 py-4 bg-gray-800 rounded-xl font-bold flex items-center">CANCEL</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>

</html>