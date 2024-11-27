<?php

namespace App\Services\Api;

use App\Exports\RoomTypeExport;
use App\Models\RoomType;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RoomTypeStatisticsService extends BaseService
{
    public function __construct()
    {
        $this->model = new RoomType();
    }

    // Lấy thống kê số lượng loại phòng
    public function getRoomTypeStatistics()
    {
        $totalRoomTypes = $this->getTotalRoomTypes();
        $roomTypesList = $this->getRoomTypesList();

        return [
            'total_room_types' => $totalRoomTypes, // Tổng số loại phòng
            'room_types_list' => $roomTypesList   // Danh sách các loại phòng
        ];
    }

    // Tổng số loại phòng
    public function getTotalRoomTypes()
    {
        return $this->model->count();
    }

    // Lấy danh sách các loại phòng
    public function getRoomTypesList()
    {
        return $this->model->select('id', 'type_name', 'description', 'price_per_hour', 'price_per_day')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // lấy danh sách dựa theo hotel_id
    public function getTotalRoomsByHotel()
    {
        $data = DB::table('room')
            ->join('room_type', 'room.room_type_id', '=', 'room_type.id')
            ->select(
                'room.hotel_id',
                'room_type.type_name',
                DB::raw('COUNT(room.id) as total_room')
            )
            ->groupBy('room.hotel_id', 'room_type.type_name')
            ->get();

        return $data;
    }

    // Xuất excel
    public function exportRoomTypes()
    {
        $roomTypes = $this->model->select('type_name', 'description', 'price_per_hour', 'price_per_day', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $export = new RoomTypeExport($roomTypes);
        $filePath = storage_path('app/public/RoomTypeStatistics.xlsx');
        file_put_contents($filePath, $export->export());

        return $filePath;
    }
}
