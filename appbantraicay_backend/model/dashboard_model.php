<?php
class Dashboard
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getStats()
    {
        $stats = [];
        // Tổng doanh thu: Chỉ tính các đơn đã giao thành công
        $resSales = $this->conn->query("SELECT SUM(total_amount) as total FROM orders_dh WHERE status = 'Đã giao'");
        $stats['totalSales'] = $resSales->fetch_assoc()['total'] ?? 0;

        // Tổng đơn hàng
        $resOrders = $this->conn->query("SELECT COUNT(id) as count FROM orders_dh");
        $stats['totalOrders'] = $resOrders->fetch_assoc()['count'] ?? 0;

        // Tổng khách hàng (loại trừ tài khoản admin)
        $resUsers = $this->conn->query("SELECT COUNT(id) as count FROM users WHERE role = 'customer' OR role = 'user'");
        $stats['totalUsers'] = $resUsers->fetch_assoc()['count'] ?? 0;

        return $stats;
    }

    public function getRevenueTrend()
    {
        // Lấy doanh thu 7 ngày gần nhất để vẽ biểu đồ
        $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as daily_total 
                FROM orders_dh 
                WHERE status = 'Đã giao' 
                GROUP BY DATE(created_at) 
                ORDER BY date ASC LIMIT 7";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTopBuyers()
    {
        $sql = "SELECT u.name, SUM(o.total_amount) as total_spent, COUNT(o.id) as order_count
                FROM users u 
                JOIN orders_dh o ON u.id = o.user_id 
                GROUP BY u.id 
                ORDER BY total_spent DESC LIMIT 5";
        $result = $this->conn->query($sql);
        $buyers = [];

        while ($row = $result->fetch_assoc()) {
            $total = (float)$row['total_spent'];
            // Logic phân hạng từ file get_order_history.php
            $rank = "Đồng";
            $color = "#94a3b8"; // Slate
            if ($total >= 10000000) {
                $rank = "Kim Cương";
                $color = "#60a5fa";
            } else if ($total >= 5000000) {
                $rank = "Vàng";
                $color = "#f59e0b";
            } else if ($total >= 1000000) {
                $rank = "Bạc";
                $color = "#e2e8f0";
            }

            $row['rank_name'] = $rank;
            $row['rank_color'] = $color;
            $buyers[] = $row;
        }
        return $buyers;
    }

    public function getRecentOrders()
    {
        // Lấy 5 đơn hàng mới nhất
        $sql = "SELECT o.*, u.name as customer_name FROM orders_dh o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC LIMIT 5";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
