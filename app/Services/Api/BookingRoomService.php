<?php

namespace App\Services\Api;

use App\Models\Booking;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Guest;
use App\Models\RoomType;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;
use App\Models\Room;
use Ramsey\Uuid\Uuid;
use App\RoomStatusEnum;

class BookingRoomService extends BaseService
{
    public function __construct()
    {
        $this->model = new Booking();
    }

    public function handleOrderRoom(Request $req)
    {
        $guestId = [];
        $transId = [];
        $roomUsingId = [];

        if (!empty($req->guests)) {
            $guest = $req->guests;
            foreach ($guest as $item) {
                $this->model = new Guest();
                $guestId[] = $this->create($item);
            }
        }

        if (!empty($req->bookings)) {
            $listRoomTypeId = $req->listRoomTypeId;
            foreach ($listRoomTypeId as $rtId) {
                $transitionDateTime = $this->convertLongToTimestamp($req->bookings['order_date']);
                $bookings = $req->bookings;
                foreach ($guestId as $value) {
                    if ($value->representative === true) {
                        $bookings['representative_id'] = $value->id;
                    }
                }
                $bookings['check_in'] = $this->convertLongToTimestamp($req->bookings['check_in']);
                $bookings['check_out'] = $this->convertLongToTimestamp($req->bookings['check_out']);
                $bookings['room_type_id'] = $rtId;
                // dd($transitionDateTime);
                $bookings['order_date'] = $transitionDateTime;
                $this->model = new Booking();
                $transId = $this->create($bookings);
            }
        }

        if (!empty($req->transition)) {
            $transitionDateTime = $this->convertLongToTimestamp($req->transition['transition_date']);
            $transition = $req->transition;
            foreach ($guestId as $value) {
                if ($value->representative === true) {
                    $transition['guest_id'] = $value->id;
                }
            }
            $transition['transition_date'] = $transitionDateTime;
            $this->model = new Transition();
            $transId = $this->create($transition);
        }

        if (!empty($req->roomUsing)) {
            $checkIn = $this->convertLongToTimestamp($req->roomUsing['check_in']);
            $roomUsing = $req->roomUsing;
            $roomUsing['trans_id'] = $transId->id;
            $roomUsing['check_in'] = $checkIn;
            $this->model = new RoomUsing();
            $roomUsingId = $this->create($roomUsing);
        }

        if (!empty($req->roomUsingGuest)) {
            $checkIn = $this->convertLongToTimestamp($req->roomUsingGuest['check_in']);
            $checkOut = null;
            if ($req->roomUsingGuest['check_out']) {
                $checkOut = $this->convertLongToTimestamp($req->roomUsingGuest['check_out']);
            }
            $roomUsingGuest = $req->roomUsingGuest;
            foreach ($guestId as $value) {
                $roomUsingGuest['uuid'] = str_replace('-', '', Uuid::uuid4()->toString());
                $roomUsingGuest['guest_id'] = $value->id;
                $roomUsingGuest['room_using_id'] = $roomUsingId->id;
                $roomUsingGuest['check_in'] = $checkIn;
                if ($checkOut) {
                    $roomUsingGuest['check_out'] = $checkOut;
                }
                $this->model = new RoomUsingGuest();
                $this->create($roomUsingGuest);
            }
        }

        if (!empty($req->roomUsingService)) {
            $roomUsingService = $req->roomUsingService;
            $this->model = new RoomUsingService();
            $this->create($roomUsingService);
        }

        if (!empty($req->roomUsing['room_id'])) {
            $this->model = new Room();
            $params = [
                "status" => RoomStatusEnum::DANG_O->value
            ];
            $this->update($req->roomUsing['room_id'], $params);
        }

        return $req->all();
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
                'b.group_name as groupName',
                DB::raw("CONCAT(b.room_quantity, ' Phòng') as roomQuantity"),
                DB::raw("CONCAT(b.guest_count, ' Khách') as guestCount"),
                'b.group_name as groupName',
                'b.status'
            )
            ->join('bookings_details as bd', 'bd.booking_id', '=', 'b.id')
            ->join('room_type as rt', 'rt.id', '=', 'bd.room_type_id')
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
