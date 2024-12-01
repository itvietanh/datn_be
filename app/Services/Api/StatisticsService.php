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
        $today = now()->toDateString(); // Lấy ngày hiện tại
        $startOfWeek = now()->startOfWeek()->toDateString(); // Lấy ngày bắt đầu tuần hiện tại
        $endOfWeek = now()->endOfWeek()->toDateString(); // Lấy ngày kết thúc tuần hiện tại

        $totalRevenue = $this->getTotalRevenue();
        $serviceUsageCount = $this->getServiceUsageCount();
        $monthlyRevenue = $this->getMonthlyRevenue();
        $dailyRevenue = $this->getDailyRevenue($today); // Doanh thu hôm nay
        $weeklyRevenue = $this->getWeeklyRevenue($startOfWeek, $endOfWeek); // Doanh thu tuần hiện tại

        return [
            'total_revenue' => $totalRevenue,
            'service_usage_count' => $serviceUsageCount,
            'monthly_revenue' => $monthlyRevenue,
            'daily_revenue' => [
                'date' => $today,
                'revenue' => $dailyRevenue,
            ],
            'weekly_revenue' => [
                'start_date' => $startOfWeek,
                'end_date' => $endOfWeek,
                'revenue' => $weeklyRevenue,
            ],
        ];
    }

    // Lấy tổng doanh thu từ bảng service
    public function getTotalRevenue()
    {
        return $this->model->sum('price'); // Tính tổng cột 'price' trong bảng service
    }

    // Tính số lần sử dụng dịch vụ
    public function getServiceUsageCount()
    {
        return $this->model->count(); // Đếm số lượng bản ghi trong bảng service
    }

    // Lấy doanh thu hàng tháng
    public function getMonthlyRevenue()
    {   
        return $this->model
            ->selectRaw('SUM(price) as total_revenue, EXTRACT(MONTH FROM created_at) as month, EXTRACT(YEAR FROM created_at) as year')
            ->groupByRaw('year, month')
            ->get();
    }

    // Lấy doanh thu theo ngày
    public function getDailyRevenue($date)
    {
        return $this->model
            ->whereDate('created_at', $date)
            ->sum('price'); // Tổng doanh thu của ngày
    }

    // Lấy doanh thu theo tuần
    public function getWeeklyRevenue($startDate, $endDate)
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('price'); // Tổng doanh thu trong khoảng thời gian (tuần)
    }
}
