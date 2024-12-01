<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\Api\ServiceService;
use App\Services\Api\StatisticsService;

class ServiceStatisticsController extends BaseController
{
    protected $service;
    protected $statisticsService;

    public function __construct(ServiceService $service, StatisticsService $statisticsService)
    {
        $this->service = $service;
        $this->statisticsService = $statisticsService;
    }

    // Phương thức để lấy tổng doanh thu
    public function totalRevenue()
    {
        $totalRevenue = $this->statisticsService->getTotalRevenue();
        $data = [
            'total_revenue' => $totalRevenue
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy số lần sử dụng dịch vụ
    public function serviceUsageCount()
    {
        $serviceUsageCount = $this->statisticsService->getServiceUsageCount();
        $data = [
            'service_usage_count' => $serviceUsageCount
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy doanh thu hàng tháng (Dựa trên khoảng ngày từ request)
    public function monthlyRevenue(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $monthlyRevenue = $this->statisticsService->getMonthlyRevenue($startDate, $endDate);

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'monthly_revenue' => $monthlyRevenue,
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy doanh thu theo ngày
    public function dailyRevenue(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $dailyRevenue = $this->statisticsService->getDailyRevenue($date);

        $data = [
            'date' => $date,
            'daily_revenue' => $dailyRevenue,
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy doanh thu theo tuần
    public function weeklyRevenue(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());

        $weeklyRevenue = $this->statisticsService->getWeeklyRevenue($startDate, $endDate);

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'weekly_revenue' => $weeklyRevenue,
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy tất cả thống kê dịch vụ
    public function allStatistics()
    {
        $statistics = $this->statisticsService->getAllStatistics();
        return $this->responseSuccess($statistics);
    }
}
