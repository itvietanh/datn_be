<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Services\Api\ServiceService;
use App\Services\Api\StatisticsService; // Nhập StatisticsService

class ServiceStatisticsController extends BaseController
{
    protected $service;
    protected $statisticsService; // Khai báo StatisticsService

    public function __construct(ServiceService $service, StatisticsService $statisticsService) {
        $this->service = $service;
        $this->statisticsService = $statisticsService; // Khởi tạo StatisticsService
    }

    // Phương thức để lấy tổng doanh thu
    public function totalRevenue()
    {
        $totalRevenue = $this->statisticsService->getTotalRevenue();
        return response()->json(['total_revenue' => $totalRevenue]);
    }

    // Phương thức để lấy số lần sử dụng dịch vụ
    public function serviceUsageCount()
    {
        $serviceUsageCount = $this->statisticsService->getServiceUsageCount();
        return response()->json(['service_usage_count' => $serviceUsageCount]);
    }

    // Phương thức để lấy doanh thu hàng tháng
    public function monthlyRevenue()
    {
        $monthlyRevenue = $this->statisticsService->getMonthlyRevenue();
        return response()->json($monthlyRevenue);
    }

     // Phương thức để lấy tất cả thống kê dịch vụ
     public function allStatistics()
     {
         $statistics = $this->statisticsService->getAllStatistics(); // Gọi phương thức lấy tất cả thống kê
         return response()->json($statistics);
     }
}