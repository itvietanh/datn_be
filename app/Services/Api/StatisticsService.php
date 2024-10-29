<?php

    namespace App\Services\Api;

use App\Models\Service;
use App\Services\BaseService;

class StatisticsService extends BaseService
{
    public function __construct()
    {
        $this->model = new Service();
    }

    // Phương thức để lấy tất cả thống kê dịch vụ
    public function getAllStatistics()
    {
        $totalRevenue = $this->getTotalRevenue();
        $serviceUsageCount = $this->getServiceUsageCount();
        $monthlyRevenue = $this->getMonthlyRevenue();

        return [
            'total_revenue' => $totalRevenue,
            'service_usage_count' => $serviceUsageCount,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }

    public function getTotalRevenue()
    {
        return $this->model->sum('service_price'); // Tính tổng doanh thu
    }

    public function getServiceUsageCount()
    {
        return $this->model->count(); // Tính số lần sử dụng dịch vụ
    }
    public function getMonthlyRevenue()
{
    return $this->model
        ->selectRaw('SUM(service_price) as total_revenue, EXTRACT(MONTH FROM created_at) as month, EXTRACT(YEAR FROM created_at) as year, service_name')
        ->groupBy('year', 'month', 'service_name') // Nhóm theo tháng, năm và tên dịch vụ
        ->get();
}


}

