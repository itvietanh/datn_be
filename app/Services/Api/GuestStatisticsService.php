<?php

namespace App\Services\Api;

use App\Models\Guest;
use App\Services\BaseService;
use Carbon\Carbon;

class GuestStatisticsService extends BaseService
{
    public function __construct()
    {
        $this->model = new Guest();
    }

    // Phương thức để lấy tất cả thống kê khách hàng
    public function getAllStatistics()
    {
        $totalGuests = $this->getTotalGuests();
        $newGuestsThisMonth = $this->getNewGuestsThisMonth();
        $activeGuests = $this->getActiveGuests();
        $inactiveGuests = $this->getInactiveGuests();

        return [
            'total_guests' => $totalGuests,
            'new_guests_this_month' => $newGuestsThisMonth,
            'active_guests' => $activeGuests,
            'inactive_guests' => $inactiveGuests,
        ];
    }

    // Tổng số khách hàng
    public function getTotalGuests()
    {
        return $this->model->count();
    }
    public function getStatisticsByDateRange($startDate, $endDate)
    {
        $startDate = Carbon::createFromFormat('Ymd', $startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('Ymd', $endDate)->format('Y-m-d');

        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_guests')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }



    // Khách mới trong tháng hiện tại
    public function getNewGuestsThisMonth()
    {
        return $this->model->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
    }

    // Khách hàng đang hoạt động (ví dụ theo trường 'representative' có dữ liệu)
    public function  getActiveGuests()
    {
        return $this->model->whereNotNull('representative')->count();
    }

    // Khách hàng không hoạt động (ví dụ theo trường 'representative' không có dữ liệu)
    public function getInactiveGuests()
    {
        return $this->model->whereNull('representative')->count();
    }
}
