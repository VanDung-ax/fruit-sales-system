<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/promotion_controller.php';

$controller = new PromotionController($conn);
$message = $_GET['msg'] ?? "";

if (isset($_GET['delete_id'])) {
    if ($controller->delete($_GET['delete_id'])) {
        header("Location: promotions.php?msg=" . urlencode("Xóa thành công!"));
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handleAdd($_POST);
    if ($result['status'] === 'success') {
        header("Location: promotions.php?msg=" . urlencode($result['message']));
        exit();
    }
    $message = $result['message'];
}

$promotions = $controller->listPromotions();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin - Promotions</title>
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
        <div class="flex items-center gap-2 mb-10">
            <div class="bg-[#13ec13] p-1.5 rounded-lg text-black font-bold">F</div>
            <span class="text-xl font-bold text-white">Fruitly Admin</span>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="dashboard.php">Dashboard</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="add_products.php">Quản lý sản phẩm</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="categories.php">Quản lý danh mục</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="promotions.php">Quản lý khuyến mãi</a></div>
            <div class="sidebar-item-active p-3 rounded-lg flex items-center gap-3">Quản lý khuyến mãi</div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="orders.php">Quản lý đơn hàng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="staffs.php">Quản lý nhân viên</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-3xl font-bold mb-8">Thêm khuyến mãi</h1>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="card-bg p-8 rounded-2xl mb-12 grid grid-cols-1 md:grid-cols-4 gap-6">
            <input type="text" name="code" class="input-dark" placeholder="Mã (ex: FRUIT2026)" required>
            <input type="number" name="discount_value" class="input-dark" placeholder="Giá trị" step="0.01" required>
            <select name="discount_type" class="input-dark">
                <option value="percentage">Phần trăm (%)</option>
                <option value="fixed">Tiền mặt (VND)</option>
            </select>
            <input type="number" name="quantity" class="input-dark" placeholder="Số lượng mã" required>
            <div class="md:col-span-1">
                <label class="block text-xs text-gray-500 mb-1 ml-2">Ngày hết hạn</label>
                <input type="date" name="expiry_date" class="input-dark" required>
            </div>
            <textarea name="description" class="input-dark md:col-span-2" placeholder="Mô tả chiến dịch..."></textarea>
            <button type="submit" class="w-full bg-[#13ec13] text-black font-bold py-4 rounded-xl hover:bg-green-400 transition md:col-span-1 self-end"> Thêm khuyến mãi</button>
        </form>

        <div class="card-bg rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#0f1f0f] text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">Mã Code</th>
                        <th class="p-4">Giảm giá</th>
                        <th class="p-4">Sử dụng</th>
                        <th class="p-4">Hạn dùng</th>
                        <th class="p-4">Trạng thái</th>
                        <th class="p-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a2e1a]">
                    <?php while ($row = $promotions->fetch_assoc()):
                        $statusClass = $row['status_text'] === 'Đang chạy' ? 'text-green-500' : 'text-red-400';
                    ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 font-bold text-[#13ec13] uppercase"><?= $row['code'] ?></td>
                            <td class="p-4"><?= number_format($row['discount_value'], 0) ?><?= $row['discount_type'] === 'percentage' ? '%' : '$' ?></td>
                            <td class="p-4 text-gray-400"><?= $row['used_count'] ?> / <?= $row['quantity'] ?></td>
                            <td class="p-4 text-gray-400"><?= date("d/m/Y", strtotime($row['expiry_date'])) ?></td>
                            <td class="p-4 font-semibold <?= $statusClass ?>"><?= $row['status_text'] ?></td>
                            <td class="p-4 text-center space-x-3">
                                <a href="?delete_id=<?= $row['id_promotion'] ?>" onclick="return confirm('Xóa mã này?')" class="text-red-500 hover:underline">Xóa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>