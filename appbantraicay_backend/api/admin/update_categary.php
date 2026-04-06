<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/category_controller.php';

$controller = new CategoryController($conn);
$message = isset($_GET['msg']) ? $_GET['msg'] : "";

if (isset($_GET['id'])) {
    $cat = $controller->getCategoryById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $result = $controller->handleUpdateCategory($_POST);
    if ($result['status'] === 'success') {
        header("Location: categories.php?msg=" . urlencode($result['message']));
        exit();
    } else {
        $message = $result['message'];
        $cat = $controller->getCategoryById($_POST['id_category']);
    }
}

if (!$cat) {
    echo "Danh mục không tồn tại!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Category - <?= htmlspecialchars($cat['name']) ?></title>
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
        <div class="flex items-center gap-2 mb-10 text-[#13ec13] font-bold text-xl">
            <div class="bg-[#13ec13] p-1.5 rounded-lg text-black font-bold">F</div>
            <span class="text-xl font-bold text-white">Fruitly <span class="text-gray-500 font-light text-sm">Admin</span></span>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="dashboard.php">Dashboard</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="add_products.php">Product</a></div>
            <div class="sidebar-item-active p-3 rounded-lg font-semibold"><a href="categories.php">Categories</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Edit Category #<?= $cat['id_category'] ?></h1>
                <a href="categories.php" class="text-gray-500 hover:text-white transition">← Back to List</a>
            </div>

            <?php if ($message): ?>
                <div id="toast-message" class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <input type="hidden" name="id_category" value="<?= $cat['id_category'] ?>">

                <div class="card-bg p-8 rounded-2xl space-y-6">
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Category Name</label>
                        <input type="text" name="name" class="input-dark" value="<?= htmlspecialchars($cat['name']) ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2">Description</label>
                        <textarea name="description" class="input-dark h-32" placeholder="Describe this category..."><?= htmlspecialchars($cat['description']) ?></textarea>
                    </div>
                </div>

                <div class="card-bg p-8 rounded-2xl flex flex-col justify-end">
                    <p class="text-sm text-gray-500 mb-4 italic">Cập nhật thông tin danh mục sẽ ảnh hưởng đến việc phân loại sản phẩm trên cửa hàng.</p>
                    <button type="submit" name="update_category" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition">
                        UPDATE CATEGORY
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Hiệu ứng ẩn thông báo tự động
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