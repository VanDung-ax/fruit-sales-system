<?php
include_once __DIR__ . "/../model/dashboard_model.php";

class DashboardController
{
    private $dashboard;

    public function __construct($db)
    {
        $this->dashboard = new Dashboard($db);
    }

    public function getData()
    {
        return [
            'stats' => $this->dashboard->getStats(),
            'revenueTrend' => $this->dashboard->getRevenueTrend(),
            'topBuyers' => $this->dashboard->getTopBuyers(),
            'recentOrders' => $this->dashboard->getRecentOrders()
        ];
    }
}
