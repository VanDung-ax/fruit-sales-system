<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/products_controller.php';
require_once __DIR__ . '/../../controller/category_controller.php'; // Thêm controller category

$controller = new ProductController($conn);
$catController = new CategoryController($conn);

$message = isset($_GET['msg']) ? $_GET['msg'] : "";

// Lấy danh sách danh mục để đổ vào Select Option
$categories_list = $catController->listCategories();

if (isset($_GET['delete_id'])) {
    if ($controller->deleteProduct($_GET['delete_id'])) {
        header("Location: add_products.php?msg=" . urlencode("Xóa sản phẩm thành công!"));
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handleAddProduct($_POST, $_FILES);
    if ($result['status'] === 'success') {
        header("Location: add_products.php?msg=" . urlencode($result['message']));
        exit();
    } else {
        $message = $result['message'];
    }
}

$products = $controller->listProducts();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin - Products</title>
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
            <span class="text-xl font-bold text-white">Fruitly <span class="text-gray-500 font-light text-sm">Admin</span></span>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer transition-all"><a href="dashboard.php" class="w-full">Dashboard</a></div>
            <div class="sidebar-item-active p-3 rounded-lg flex items-center gap-3 font-semibold"><a href="add_products.php" class="w-full">Quản lý sản phẩm</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="categories.php" class="w-full">Quản lý danh mục</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="users.php" class="w-full">Quản lý người dùng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="promotions.php" class="w-full">Quản lý khuyến mãi</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="orders.php" class="w-full">Quản lý đơn hàng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="staffs.php" class="w-full">Quản lý nhân viên</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-8">Thêm sản phẩm</h1>

        <?php if ($message): ?>
            <div id="toast-message" class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div class="card-bg p-8 rounded-2xl space-y-6">
                <input type="text" name="name" class="input-dark" placeholder="Name" required>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" step="0.01" name="price" class="input-dark" placeholder="Price ($)" required>
                    <input type="number" name="stock" class="input-dark" placeholder="Stock" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Category</label>
                    <select name="category" class="input-dark" required>
                        <option value="">Select Category</option>
                        <?php while ($cat = $categories_list->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="card-bg p-8 rounded-2xl flex flex-col justify-between text-center border-2 border-dashed border-green-900/30">
                <img id="imgPreview" class="hidden max-h-32 mx-auto rounded-lg mb-4" alt="Preview">
                <input type="file" name="image" id="fileInput" class="hidden" required accept="image/*">
                <label for="fileInput" class="cursor-pointer">
                    <span class="block text-3xl mb-2">📸</span>
                    <span class="text-xs text-gray-500">Upload product image</span>
                </label>
                <button type="submit" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl mt-8 hover:bg-green-400 transition">Thêm sản phẩm</button>
            </div>
        </form>

        <div class="card-bg rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#0f1f0f] text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Image</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Price</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a2e1a]">
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 text-gray-500 font-mono">#<?= $row['id'] ?></td>
                            <td class="p-4"><img src="../../images/<?= $row['image'] ?>" class="w-12 h-12 object-cover rounded-lg"></td>
                            <td class="p-4 font-bold text-white"><?= $row['name'] ?></td>
                            <td class="p-4 text-gray-400"><?= $row['category'] ?></td>
                            <td class="p-4 text-[#13ec13] font-bold">$<?= number_format($row['price'], 2) ?></td>
                            <td class="p-4 text-center space-x-3">
                                <a href="update_product.php?id=<?= $row['id'] ?>" class="text-blue-500 hover:text-blue-400 font-semibold">Edit</a>
                                <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Xóa?')" class="text-red-500 hover:text-red-400 font-semibold">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        document.getElementById('fileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imgPreview');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

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