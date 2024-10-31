<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\GuestStatisticsService;
use Illuminate\Http\Request;

class GuestStatisticsController extends BaseController
{
    protected $guestStatisticsService;

    public function __construct(GuestStatisticsService $guestStatisticsService)
    {
        $this->guestStatisticsService = $guestStatisticsService;
    }

    // Phương thức để lấy tổng số khách
    public function totalGuests()
    {
        $totalGuests = $this->guestStatisticsService->getTotalGuests();
        $data = [
            'total_guests' => $totalGuests
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy số khách mới trong tháng
    public function newGuestsThisMonth()
    {
        $newGuests = $this->guestStatisticsService->getNewGuestsThisMonth();
        $data = [
            'new_guests_this_month' => $newGuests
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy số khách đang hoạt động
    public function activeGuests()
    {
        $activeGuests = $this->guestStatisticsService->getActiveGuests();
        $data = [
            'active_guests' => $activeGuests
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy tất cả thống kê khách hàng
    public function allStatistics()
    {
        $statistics = $this->guestStatisticsService->getAllStatistics();
        return response()->json($statistics);
    }
}
