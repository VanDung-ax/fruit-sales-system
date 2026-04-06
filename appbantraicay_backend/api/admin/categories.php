<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/category_controller.php';

$controller = new CategoryController($conn);
$message = isset($_GET['msg']) ? $_GET['msg'] : "";

if (isset($_GET['delete_id'])) {
    if ($controller->deleteCategory($_GET['delete_id'])) {
        header("Location: categories.php?msg=" . urlencode("Xóa danh mục thành công!"));
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handleAddCategory($_POST);
    if ($result['status'] === 'success') {
        header("Location: categories.php?msg=" . urlencode($result['message']));
        exit();
    } else {
        $message = $result['message'];
    }
}

$categories = $controller->listCategories();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin - Categories</title>
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
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="dashboard.php" class="w-full">Dashboard</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="add_products.php" class="w-full">Quản lý sản phẩm</a></div>
            <div class="sidebar-item-active p-3 rounded-lg flex items-center gap-3 font-semibold"><a href="" class="w-full">Quản lý danh mục</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="users.php" class="w-full">Quản lý người dùng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="promotions.php" class="w-full">Quản lý khuyến mãi</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="orders.php" class="w-full">Quản lý đơn hàng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="staffs.php" class="w-full">Quản lý nhân viên</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-8">Thêm danh mục</h1>

        <?php if ($message): ?>
            <div id="toast-message" class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="md:col-span-2 card-bg p-8 rounded-2xl space-y-6">
                <input type="text" name="name" class="input-dark" placeholder="Tên danh mục" required>
                <textarea name="description" class="input-dark h-24" placeholder="Mô tả"></textarea>
            </div>
            <div class="card-bg p-8 rounded-2xl flex items-end">
                <button type="submit" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition">Thêm danh mục</button>
            </div>
        </form>

        <div class="card-bg rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#0f1f0f] text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Category Name</th>
                        <th class="p-4">Description</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a2e1a]">
                    <?php while ($row = $categories->fetch_assoc()): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500 font-mono">#<?= $row['id_category'] ?></td>
                            <td class="p-4 font-bold text-white"><?= $row['name'] ?></td>
                            <td class="p-4 text-gray-400"><?= $row['description'] ?></td>
                            <td class="p-4 text-center space-x-3">
                                <a href="update_categary.php?id=<?= $row['id_category'] ?>" class="text-blue-500 hover:text-blue-400 font-semibold">Edit</a>
                                <a href="?delete_id=<?= $row['id_category'] ?>" onclick="return confirm('Xóa?')" class="text-red-500 hover:text-red-400 font-semibold">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
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