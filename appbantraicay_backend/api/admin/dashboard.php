<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/dashboard_controller.php';

$controller = new DashboardController($conn);
$data = $controller->getData();

$stats = $data['stats'];
$recentOrders = $data['recentOrders'];
$topBuyers = $data['topBuyers'];
$trend = $data['revenueTrend'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Fruitly Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </style>
</head>

<body class="flex h-screen overflow-hidden">
    <aside class="w-64 card-bg flex flex-col p-6">
        <div class="flex items-center gap-2 mb-10">
            <div class="bg-[#13ec13] p-1.5 rounded-lg text-black font-bold">F</div>
            <span class="text-xl font-bold text-white">Fruitly Admin</span>
        </div>
        <nav class="flex-1 space-y-2 text-sm">
            <div class="sidebar-item-active p-3 rounded-lg flex items-center gap-3">Dashboard</div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="add_products.php">Quản lý sản phẩm</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="categories.php">Quản lý danh mục</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="users.php">Quản lý người dùng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="orders.php">Quản lý đơn hàng</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="promotions.php">Quản lý khuyến mãi</a></div>
            <div class="p-3 text-gray-500 hover:text-white cursor-pointer"><a href="staffs.php">Quản lý nhân viên</a></div>
        </nav>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="card-bg p-6 rounded-2xl">
                <p class="text-gray-500 text-sm">Tổng doanh thu</p>
                <h3 class="text-2xl font-bold text-[#13ec13] mt-1"><?= number_format($stats['totalSales'], 0, ',', '.') ?>đ</h3>
                <p class="text-xs text-gray-600 mt-2">Dựa trên đơn đã giao thành công</p>
            </div>
            <div class="card-bg p-6 rounded-2xl">
                <p class="text-gray-500 text-sm">Tổng đơn hàng</p>
                <h3 class="text-2xl font-bold text-white mt-1"><?= $stats['totalOrders'] ?></h3>
                <p class="text-xs text-blue-500 mt-2">Tất cả trạng thái</p>
            </div>
            <div class="card-bg p-6 rounded-2xl">
                <p class="text-gray-500 text-sm">Khách hàng</p>
                <h3 class="text-2xl font-bold text-white mt-1"><?= $stats['totalUsers'] ?></h3>
                <p class="text-xs text-gray-600 mt-2">Người dùng đăng ký hệ thống</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="col-span-2 card-bg p-6 rounded-2xl h-80 flex flex-col">
                <h4 class="font-bold text-white mb-4">Xu hướng doanh thu (7 ngày)</h4>
                <div class="flex-1">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="card-bg p-6 rounded-2xl overflow-y-auto max-h-80">
                <h4 class="font-bold text-white mb-4 flex items-center gap-2">🏆 Top người mua</h4>
                <div class="space-y-4">
                    <?php foreach ($topBuyers as $buyer): ?>
                        <div class="flex items-center gap-3 bg-white/5 p-3 rounded-xl border border-white/5">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-white"><?= htmlspecialchars($buyer['name']) ?></p>
                                <p class="text-[10px] font-bold" style="color: <?= $buyer['rank_color'] ?>">Hạng: <?= $buyer['rank_name'] ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-white"><?= number_format($buyer['total_spent'], 0) ?>đ</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card-bg p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-white">Đơn đặt hàng gần đây</h4>
                <a href="orders.php" class="text-[#13ec13] text-sm hover:underline">Xem tất cả</a>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="text-gray-500 text-xs uppercase border-b border-gray-800">
                    <tr>
                        <th class="pb-4">Mã Đơn</th>
                        <th class="pb-4">Khách hàng</th>
                        <th class="pb-4">Tổng tiền</th>
                        <th class="pb-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    <?php foreach ($recentOrders as $order): ?>
                        <tr class="border-b border-gray-800/50 hover:bg-white/5 transition">
                            <td class="py-4 font-mono text-[#13ec13]">#<?= $order['id'] ?></td>
                            <td class="py-4"><?= htmlspecialchars($order['customer_name'] ?? 'Ẩn danh') ?></td>
                            <td class="py-4 text-white font-bold"><?= number_format($order['total_amount'], 0) ?>đ</td>
                            <td class="py-4">
                                <?php
                                $statusClass = ($order['status'] == 'Đã giao') ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500';
                                ?>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold <?= $statusClass ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Cấu hình Biểu đồ Chart.js
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const trendData = <?= json_encode($trend) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.map(d => d.date),
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: trendData.map(d => d.daily_total),
                    borderColor: '#13ec13',
                    backgroundColor: 'rgba(19, 236, 19, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#1a2e1a'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html>