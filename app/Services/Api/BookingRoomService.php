<?php

namespace App\Services\Api;

use App\Models\Booking;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingRoomService extends BaseService
{
    public function __construct()
    {
        $this->model = new Booking();
    }

    public function getBookingList(Request $request)
    {
        $query = DB::table('bookings as b')
            ->select(
                'rt.type_name as typeName',
                'r.room_number as roomNumber',
                'g.name as guestName',
                'b.order_date as orderDate',
                'b.check_in as checkIn',
                'b.check_out as checkOut',
                DB::raw("CONCAT(b.room_quantity, ' Phòng') as roomQuantity"),
                DB::raw("CONCAT(b.guest_count, ' Khách') as guestCount"),
                'b.group_name as groupName',
                'b.status'
            )
            ->join('room_type as rt', 'rt.id', '=', 'b.room_type_id')
            ->leftJoin('room_using as ru', 'ru.id', '=', 'b.room_using_id')
            ->leftJoin('room as r', 'r.id', '=', 'ru.room_id')
            ->leftJoin('guest as g', 'g.id', '=', 'b.representative_id');

        if ($request->has('room_type_id')) {
            $query->where('b.room_type_id', $request->query('room_type_id'));
        }

        // Tìm ngày đặt
        if ($request->has('dateFrom') && $request->has('dateTo')) {
            $query->whereBetween('b.check_in', [$request->query('dateFrom'), $request->query('dateTo')]);
        }

        // Tìm ngày trả phòng
        if ($request->has('dateFrom') && $request->has('dateTo')) {
            $query->whereBetween('b.check_out', [$request->query('dateFrom'), $request->query('dateTo')]);
        }

        if ($request->has('guestName')) {
            $query->where('g.name', 'like', '%' . $request->query('guestName') . '%');
        }

        return $this->getListQueryBuilder($request, $query);
    }
}
