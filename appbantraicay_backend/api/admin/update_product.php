<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/products_controller.php';

$controller = new ProductController($conn);
$message = isset($_GET['msg']) ? $_GET['msg'] : "";

if (isset($_GET['id'])) {
    $product = $controller->getProductById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $result = $controller->handleUpdateProduct($_POST, $_FILES);
    if ($result['status'] === 'success') {
        header("Location: add_products.php?msg=" . urlencode($result['message']));
        exit();
    } else {
        $message = $result['message'];
        $product = $controller->getProductById($_POST['id']);
    }
}

if (!$product) {
    echo "Sản phẩm không tồn tại!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product - <?= htmlspecialchars($product['name']) ?></title>
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
    <aside class="w-64 card-bg p-6">
        <div class="flex items-center gap-2 mb-10 text-[#13ec13] font-bold text-xl">Fruitly Admin</div>
        <nav class="space-y-2">
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="dashboard.php">Dashboard</a></div>
            <div class="sidebar-item-active p-3 rounded-lg font-semibold"><a href="add_products.php">Product</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Edit Product #<?= $product['id'] ?></h1>
                <a href="add_products.php" class="text-gray-500 hover:text-white transition">← Back to List</a>
            </div>

            <?php if ($message): ?>
                <div id="toast-message" class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="card-bg p-8 rounded-2xl space-y-4">
                    <input type="text" name="name" class="input-dark" value="<?= htmlspecialchars($product['name']) ?>" required>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" step="0.01" name="price" class="input-dark" value="<?= $product['price'] ?>" required>
                        <input type="number" name="stock" class="input-dark" value="<?= $product['stock'] ?>" required>
                    </div>
                    <select name="category" class="input-dark">
                        <option value="Apples" <?= $product['category'] == 'Apples' ? 'selected' : '' ?>>Apples</option>
                        <option value="Berries" <?= $product['category'] == 'Berries' ? 'selected' : '' ?>>Berries</option>
                        <option value="Tropical" <?= $product['category'] == 'Tropical' ? 'selected' : '' ?>>Tropical</option>
                    </select>
                </div>

                <div class="card-bg p-8 rounded-2xl flex flex-col justify-between">
                    <div class="flex items-center gap-4 mb-4">
                        <img src="../../images/<?= $product['image'] ?>" class="w-20 h-20 object-cover rounded-lg">
                        <p class="text-xs text-gray-500">Current image. Upload new to change.</p>
                    </div>
                    <input type="file" name="image" class="text-sm text-gray-500">
                    <button type="submit" name="update_product" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl mt-6 hover:bg-green-400 transition">
                        UPDATE PRODUCT
                    </button>
                </div>
            </form>
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