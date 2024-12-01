<?php

namespace App\Services\Api;

use App\Models\RoomType;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class RoomtypeStatisticsService extends BaseService
{
    public function __construct()
    {
        $this->model = new RoomType();
    }

    // Lấy tất cả thống kê
    public function getAllStatistics()
    {
        return [
            'total_room_types' => $this->getTotalRoomTypes(), // Tổng số loại phòng
            'total_rooms' => $this->getTotalRooms(), // Tổng số phòng hiện có
            'room_types_statistics' => $this->getRoomtypeData() // Thống kê chi tiết theo loại phòng
        ];
    }

    // Tổng số loại phòng
    public function getTotalRoomTypes()
    {
        return $this->model->count(); // Đếm tổng số loại phòng
    }

    // Tổng số phòng hiện có
    public function getTotalRooms()
    {
        return DB::table('room')->count(); // Đếm tổng số phòng từ bảng room
    }

    // Lấy thống kê loại phòng
    public function renderDataStatisticalRoomtype($req)
    {
        $totalRoomTypes = $this->getTotalRoomTypes();
        $statistical = $this->getRoomtypeData(); // Lấy dữ liệu thống kê loại phòng

        return [
            'statistical' => $statistical,
            'total_room_types' => $totalRoomTypes
        ];
    }

    // Lấy dữ liệu thống kê loại phòng (không cần tham số ngày)
    public function getRoomtypeData()
    {
        try {
            // Truy vấn dữ liệu từ bảng room_type kết hợp với bảng room
            $query = DB::table('room_type as rt')
                ->leftJoin('room as r', 'rt.id', '=', 'r.room_type_id') // Kết hợp bảng room với room_type
                ->select(
                    'rt.type_name as roomTypeName', // Tên loại phòng
                    DB::raw('COUNT(r.id) as totalRooms') // Đếm số phòng từ bảng room
                )
                ->groupBy('rt.id', 'rt.type_name') // Nhóm theo loại phòng
                ->orderBy('rt.type_name', 'asc') // Sắp xếp theo tên loại phòng
                ->get(); // Lấy tất cả dữ liệu

            return $query;
        } catch (\Exception $e) {
            \Log::error('Error fetching room type data: ' . $e->getMessage());
            throw new \Exception('An error occurred while fetching room type data.');
        }
    }

    // Lấy thống kê chi tiết theo loại phòng, trả về kết quả cho API
    public function getRoomtypeStatisticsForApi()
    {
        try {
            // Lấy thống kê dữ liệu loại phòng
            $roomTypes = $this->getRoomtypeData();

            return [
                'code' => 'OK',
                'message' => 'Success',
                'data' => $roomTypes,
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching room type data for API: ' . $e->getMessage());
            return [
                'code' => 'ERROR',
                'message' => 'An error occurred while fetching room type data.',
            ];
        }
    }
}
