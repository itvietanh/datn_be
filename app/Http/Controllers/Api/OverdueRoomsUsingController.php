<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\OverdueRoomsUsing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverdueRoomsUsingController extends BaseController
{
    protected $RoomUsing;

    public function __construct(OverdueRoomsUsing $RoomUsing)
    {
        $this->RoomUsing = $RoomUsing;
    }

    public function getOverdueRooms(Request $req)
    {
        $columns = ['uuid', 'trans_id', 'room_id', 'check_in', 'check_out', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        // Gọi service để lấy danh sách dữ liệu
        $data = $this->RoomUsing->getList($req, $columns);

        // Lọc ra danh sách phòng quá hạn
        $overdueRooms = $data->filter(function ($room) {
            return \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($room->check_out));
        });

        if ($overdueRooms->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Không có phòng nào quá hạn.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Có ' . $overdueRooms->count() . ' phòng quá hạn.',
            'data' => $overdueRooms
        ], 200);
    }

    public function index(Request $request)
    {
        $query = DB::table('room_using as ru')
            ->select(
                'ru.*'
            )
            ->join('room as r', 'ru.room_id', '=', 'r.id')
            ->where("r.status",  1)
            ->where(DB::raw('CURRENT_DATE'), '>', DB::raw('ru.check_out'));
        $data = $this->RoomUsing->getListQueryBuilder($request, $query);

        return $this->getPaging($data);
    }
}
