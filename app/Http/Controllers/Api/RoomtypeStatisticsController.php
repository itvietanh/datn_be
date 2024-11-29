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
    public function roomtypeStatistical(Request $request)
    {
        try {
            $response = $this->roomtypeStatisticsService->renderDataStatisticalRoomtype($request);
            return $this->responseSuccess($response);
        } catch (Exception $e) {
            \Log::error('Error fetching room type statistics: ' . $e->getMessage());
            return $this->responseError('An error occurred while fetching room type statistics.', 500);
        }
    }

    /**
     * Lấy tổng số phòng theo loại phòng (không cần tham số ngày)
     */
    public function getTotalRoomsByHotel(Request $request)
{
    try {
        // Truy vấn lấy tổng số phòng theo loại phòng từ bảng room_type và room
        $query = DB::table('room_type as rt')
            ->leftJoin('room as r', 'r.room_type_id', '=', 'rt.id') // Kết hợp bảng room với room_type
            ->select(
                'rt.type_name as roomTypeName', // Tên loại phòng
                DB::raw('COUNT(r.id) as totalRooms') // Đếm số lượng phòng trong bảng room
            )
            ->groupBy('rt.id', 'rt.type_name') // Nhóm theo loại phòng
            ->orderBy('rt.type_name', 'asc') // Sắp xếp theo tên loại phòng
            ->get(); // Lấy tất cả dữ liệu

        // Trả về kết quả dưới dạng JSON
        return $this->responseSuccess(['data' => $query]);
    } catch (Exception $e) {
        \Log::error('Error fetching total rooms data: ' . $e->getMessage());
        return $this->responseError('An error occurred while fetching total rooms data.', 500);
    }
}


    /**
     * Export thống kê loại phòng ra Excel
     */
    public function exportRoomTypeData(Request $request)
    {
        try {
            // Lấy dữ liệu thống kê loại phòng
            $data = $this->roomtypeStatisticsService->renderDataStatisticalRoomtype($request);

            // Thêm thông tin ngày tháng vào dữ liệu (nếu cần)
            // Nếu không cần thông tin ngày tháng, bạn có thể bỏ qua đoạn mã này
            $data['dateFrom'] = \DateTime::createFromFormat('Ymd', $request->dateFrom)->format('d-m-Y');
            $data['dateTo'] = \DateTime::createFromFormat('Ymd', $request->dateTo)->format('d-m-Y');

            // Tạo đối tượng export và nhận dữ liệu
            $export = new StatisticalExport($data);

            // Tạo nội dung file Excel
            $fileContent = $export->template();

            // Trả về file Excel cho người dùng tải về
            return response($fileContent)
                ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->header('Content-Disposition', 'attachment; filename="thong-ke-loai-phong.xlsx"');
        } catch (Exception $e) {
            \Log::error('Error exporting room type data: ' . $e->getMessage());
            return $this->responseError('An error occurred while exporting room type data.', 500);
        }
    }
}
