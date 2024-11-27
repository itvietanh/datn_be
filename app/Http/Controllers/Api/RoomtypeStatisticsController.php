<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\RoomTypeStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RoomtypeStatisticsController extends BaseController
{
    protected $roomTypeStatisticsService;

    public function __construct(RoomTypeStatisticsService $roomTypeStatisticsService)
    {
        $this->roomTypeStatisticsService = $roomTypeStatisticsService;
    }

    /** 
     * Thống kê loại phòng
     * API: GET roomtype/
     */
    public function roomtypeStatistical()
    {
        $statistics = $this->roomTypeStatisticsService->getRoomTypeStatistics();
        return response()->json($statistics);
    }

    /** 
     * Thống kê loại phòng
     * API: GET /roomtype/total-roomtype
     */
    public function getTotalRoomsByHotel()
    {
        $data = $this->roomTypeStatisticsService->getTotalRoomsByHotel();
        return response()->json($data, 200);
    }

    /**
     * Xuất thống kê loại phòng ra file Excel
     * API: GET /roomtype/export-roomtype
     */
    public function exportRoomTypeData()
    {
        // Gọi service để xử lý xuất dữ liệu
        $file = $this->roomTypeStatisticsService->exportRoomTypes();

        // Trả file về cho người dùng
        return response()->download($file, 'RoomTypeStatistics.xlsx')->deleteFileAfterSend(true);
    }
}
