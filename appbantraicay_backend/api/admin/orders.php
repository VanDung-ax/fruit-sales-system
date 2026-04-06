<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/oder_controller.php';

// Ở đầu file admin/orders.php
$controller = new OrderController($conn);
$message = "";

// Nếu có yêu cầu cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $result = $controller->handleUpdateStatus($_POST);
    $message = $result['message'];
}

// Nếu có yêu cầu xóa
if (isset($_GET['delete_id'])) {
    $result = $controller->handleDelete($_GET['delete_id']);
    $message = $result['message'];
}

// Lấy danh sách để hiển thị (có lọc theo search_id nếu có)
$search_id = isset($_GET['search_id']) && $_GET['search_id'] !== '' ? (int)$_GET['search_id'] : null;
$orders = $controller->listOrders($search_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin - Orders</title>
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
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="users.php">Quản lý người dùng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="promotions.php">Quản lý khuyến mãi</a></div>
            <div class="sidebar-item-active p-3 rounded-lg flex items-center gap-3">Quản lý đơn hàng</div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="staffs.php">Quản lý nhân viên</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Quản lý đơn hàng</h1>

            <form action="" method="GET" class="flex gap-2">
                <input type="number" name="search_id" class="input-dark w-64" placeholder="Nhập mã đơn hàng (ID)..." value="<?= $search_id ?>">
                <button type="submit" class="bg-[#13ec13] text-black px-6 font-bold rounded-xl hover:bg-green-400 transition">Tìm kiếm</button>
                <?php if ($search_id): ?>
                    <a href="orders.php" class="bg-gray-800 text-white px-4 py-3 rounded-xl">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-900/20 text-green-500 border border-green-900"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="card-bg rounded-2xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#0f1f0f] text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Khách hàng</th>
                        <th class="p-4">Số điện thoại</th>
                        <th class="p-4">Tổng tiền</th>
                        <th class="p-4">Thanh toán</th>
                        <th class="p-4">Trạng thái</th>
                        <th class="p-4">Ngày đặt</th>
                        <th class="p-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1a2e1a]">
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 font-bold text-[#13ec13]">#<?= $row['id'] ?></td>
                            <td class="p-4 font-semibold"><?= htmlspecialchars($row['full_name']) ?></td>
                            <td class="p-4 text-gray-400"><?= $row['phone'] ?></td>
                            <td class="p-4 text-white font-bold"><?= number_format($row['total_amount'], 0, ',', '.') ?>đ</td>
                            <td class="p-4">
                                <span class="text-xs <?= $row['payment_status'] == 'Đã thanh toán' ? 'text-blue-400' : 'text-orange-400' ?>">
                                    <?= $row['payment_method'] ?> (<?= $row['payment_status'] ?>)
                                </span>
                            </td>
                            <td class="p-4">
                                <?php
                                $statusColor = "bg-gray-800 text-gray-300";
                                if ($row['status'] == 'Đang chờ') $statusColor = "bg-yellow-900/30 text-yellow-500";
                                if ($row['status'] == 'Đã giao') $statusColor = "bg-green-900/30 text-green-500";
                                ?>
                                <span class="status-badge <?= $statusColor ?>"><?= $row['status'] ?></span>
                            </td>
                            <td class="p-4 text-gray-500 text-xs"><?= date("d/m/Y H:i", strtotime($row['created_at'])) ?></td>
                            <td class="p-4 text-center">
                                <form action="" method="POST" class="flex gap-2 justify-center">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                                    <?php
                                    // Kiểm tra nếu trạng thái hiện tại là 'Đã giao'
                                    $isDelivered = ($row['status'] == 'Đã giao');
                                    // Bạn cũng có thể khóa luôn nếu đã 'Đã hủy'
                                    $isCancelled = ($row['status'] == 'Đã hủy');
                                    ?>

                                    <select name="status" class="bg-[#112211] border border-[#1a2e1a] text-xs p-1 rounded">
                                        <option value="Đang chờ"
                                            <?= $row['status'] == 'Đang chờ' ? 'selected' : '' ?>
                                            <?= $isDelivered ? 'disabled' : '' ?>>Đang chờ</option>

                                        <option value="Xác nhận"
                                            <?= $row['status'] == 'Xác nhận' ? 'selected' : '' ?>
                                            <?= $isDelivered ? 'disabled' : '' ?>>Xác nhận</option>

                                        <option value="Đã giao"
                                            <?= $row['status'] == 'Đã giao' ? 'selected' : '' ?>>Đã giao</option>

                                        <option value="Đã hủy"
                                            <?= $row['status'] == 'Đã hủy' ? 'selected' : '' ?>>Hủy bỏ</option>
                                    </select>

                                    <button type="submit" name="update_status"
                                        class="text-[#13ec13] text-xs hover:underline <?= ($isCancelled) ? 'hidden' : '' ?>">
                                        Lưu
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>