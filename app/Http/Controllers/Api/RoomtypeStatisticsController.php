<?php

namespace App\Http\Controllers\Api;

use App\Exports\StatisticalExport;
use App\Http\Controllers\BaseController;
use App\Services\Api\RoomtypeStatisticsService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class RoomtypeStatisticsController extends BaseController
{
    protected $roomtypeStatisticsService;

    public function __construct(RoomtypeStatisticsService $roomtypeStatisticsService)
    {
        $this->roomtypeStatisticsService = $roomtypeStatisticsService;
    }

    /**
     * Lấy thống kê loại phòng
     */


    /**
     * Lấy tổng số phòng theo loại phòng (không cần tham số ngày)
     */
    public function getTotalRoomsByHotel(Request $request)
    {
     
            // Thực hiện truy vấn SQL
            $roomTypeStats = DB::table('room_type as rt')
                ->leftJoin('room as r', 'r.room_type_id', '=', 'rt.id')
                ->select(
                    'rt.type_name as room_type_name',
                    DB::raw('COUNT(r.id) as total_rooms')
                )
                ->groupBy('rt.id', 'rt.type_name')
                ->get();

            // Trả về kết quả dạng JSON
            return response()->json([
                'success' => true,
                'data' => $roomTypeStats,
            ], 200);

    }

    public function roomtypeStatistical(Request $request)
    {

        $response = $this->roomtypeStatisticsService->renderDataStatisticalRoomtype($request);
        return $this->responseSuccess($response);
    }
    /**
     * Export thống kê loại phòng ra Excel
     */
    public function exportExcelStatistical(Request $req)
    {
        $data = $this->roomtypeStatisticsService->renderDataStatisticalRoomtype($req);
        $data['dateFrom'] = \DateTime::createFromFormat('Ymd', $req->dateFrom)->format('d-m-Y');
        $data['dateTo'] = \DateTime::createFromFormat('Ymd', $req->dateTo)->format('d-m-Y');
        $export = new StatisticalExport($data);

        $fileContent = $export->template();

        // Trả về file Excel
        return response($fileContent)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="thong-ke-loai-phong.xlsx"');
    }
}
