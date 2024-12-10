<?php

namespace App\Services\Api;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Guest;
use App\Models\BookingGuest;

class BookingRoomService extends BaseService
{
    public function __construct()
    {
        $this->model = new Booking();
    }

    public function handleOrderRoom(Request $req)
    {
        DB::beginTransaction();

        try {
            $guestId = [];
            $bookingsId = [];

            if (!empty($req->guests)) {
                $guest = $req->guests;
                foreach ($guest as $item) {
                    $this->model = new Guest();
                    $guestId[] = $this->create($item);
                }
            }

            $bookings = $req->bookings;
            if (!empty($req->bookings)) {
                $transitionDateTime = $this->convertLongToTimestamp($req->bookings['order_date']);
                foreach ($guestId as $value) {
                    if ($value->representative === true) {
                        $bookings['representative_id'] = $value->id;
                    }
                }
                $bookings['check_in'] = $this->convertLongToTimestamp($req->bookings['check_in']);
                $bookings['check_out'] = $this->convertLongToTimestamp($req->bookings['check_out']);
                $bookings['order_date'] = $transitionDateTime;
                $this->model = new Booking();
                $bookingsId = $this->create($bookings);

                $bookingDetail = $req->bookingDetail;
                foreach ($bookingDetail as $detail) {
                    $this->model = new BookingDetail();
                    $bookingDetail['booking_id'] = $bookingsId->id;
                    $bookingDetail['room_type_id'] = $detail['room_type_id'];
                    $bookingDetail['quantity'] = $detail['quantity'];
                    $this->create($bookingDetail);
                }

                foreach ($guestId as $value) {
                    $this->model = new BookingGuest();
                    $bookingGuestData = [
                        'booking_id' => $bookingsId->id,
                        'guest_id' => $value->id,
                    ];
                    $this->create($bookingGuestData);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Success', 'data' => $req->all()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }


    public function getBookingList(Request $request)
    {
        $query = DB::table('bookings as b')
            ->select(
                'b.id',
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
            // ->join('bookings_details as bd', 'bd.booking_id', '=', 'b.id')
            // ->join('room_type as rt', 'rt.id', '=', 'bd.room_type_id')
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

    public function getBookingRoom(Request $request)
    {
        $query = DB::table('bookings as b')
            ->select(
                'b.check_in as checkIn',
                'b.check_out as checkOut',
                'b.note',
                'b.group_name as groupName',
                'b.representative_id as representativeId',
                'b.contract_type as contractType',
                'g.uuid',
                'g.name',
                'g.phone_number as phoneNumber',
                'g.province_id as provinceId',
                'g.district_id as districtId',
                'g.ward_id as wardId'
            )
            ->join('guest as g', 'b.representative_id', '=', 'g.id')
            ->where('g.representative', '=', true)
            ->where('b.id', '=', $request->id);

        return $this->getOneQueryBuilder($query);
    }

    public function getGuestInBooking($req)
    {
        $query = DB::table('bookings as b')
            ->select("g.*")
            ->join('booking_guest as bg', 'b.id', 'bg.booking_id')
            ->join('guest as g', 'g.id', 'bg.guest_id')
            ->where('b.id', $req->id);

        return $this->getListQueryBuilder($req, $query);
    }

    public function getRoomType($req)
    {
        $query = DB::table('bookings as b')
            ->select(
                'rt.id as rtId',
                'rt.uuid as rtUuid',
                'r.uuid as rUuid',
                'b.status',
                'rt.type_name as roomTypeName',
                DB::raw('COALESCE(r.room_number, NULL) as roomNumber')
            )
            ->leftJoin('bookings_details as bd', 'b.id', '=', 'bd.booking_id')
            ->leftJoin('room_type as rt', 'bd.room_type_id', '=', 'rt.id')
            ->leftJoin('room_using as ru', function ($join) {
                $join->on('b.room_using_id', '=', 'ru.id')
                    ->whereNotNull('ru.check_in');
            })
            ->leftJoin('room as r', 'ru.room_id', '=', 'r.id');

        return $this->getListQueryBuilder($req, $query);
    }
}
