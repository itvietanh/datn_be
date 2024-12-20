<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Models\Guest;
use App\Services\Api\GuestStatisticsService;
use Illuminate\Http\Request;

class GuestStatisticsController extends BaseController
{
    protected $guestStatisticsService;

    public function __construct(GuestStatisticsService $guestStatisticsService)
    {
        $this->guestStatisticsService = $guestStatisticsService;
    }
    // Phương thức để lấy số khách hàng theo từng tháng
    // Phương thức để lấy số khách hàng theo từng ngày
    public function getGuestStatistics(Request $request)
    {
        // Lấy tham số start_date và end_date từ request
        $startDate = $request->query('dateFrom');
        $endDate = $request->query('dateTo');

        // Kiểm tra nếu thiếu tham số
        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng cung cấp ngày (start_date và end_date).',
            ], 400);
        }

        try {
            // Sử dụng service để lấy dữ liệu
            $guestStatisticsService = new \App\Services\Api\GuestStatisticsService();
            $guestCount = $guestStatisticsService->getStatisticsByDateRange($startDate, $endDate);

            return response()->json([
                'status' => 'success',
                'data' => $guestCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




    // Phương thức để lấy tổng số khách
    public function totalGuests()
    {
        $totalGuests = $this->guestStatisticsService->getTotalGuests();
        if (empty($totalGuests)) {
            return response()->json(['error' => 'No guests found'], 404);
        }

        $data = [
            'total_guests' => $totalGuests
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy số khách mới trong tháng
    public function newGuestsThisMonth()
    {
        $newGuests = $this->guestStatisticsService->getNewGuestsThisMonth();
        if (empty($newGuests)) {
            return response()->json(['error' => 'No new guests found this month'], 404);
        }

        $data = [
            'new_guests_this_month' => $newGuests
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy số khách đang hoạt động
    public function activeGuests()
    {
        $activeGuests = $this->guestStatisticsService->getActiveGuests();
        if (empty($activeGuests)) {
            return response()->json(['error' => 'No active guests found'], 404);
        }

        $data = [
            'active_guests' => $activeGuests
        ];
        return $this->responseSuccess($data);
    }

    // Phương thức để lấy tất cả thống kê khách hàng
    public function allStatistics()
    {
        $statistics = $this->guestStatisticsService->getAllStatistics();

        if (
            empty($statistics['total_guests']) &&
            empty($statistics['new_guests_this_month']) &&
            empty($statistics['active_guests']) &&
            empty($statistics['inactive_guests'])
        ) {
            return response()->json(['error' => 'No statistics found'], 404);
        }

        $data = [
            'all' => $statistics
        ];
        return response()->json($data);
    }
}
