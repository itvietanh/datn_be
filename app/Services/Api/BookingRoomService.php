<?php

namespace App\Services\Api;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Guest;
use App\Models\BookingGuest;
use App\Models\Room;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\Transition;
use App\RoomStatusEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

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

    public function OrderRoom(Request $req)
    {
        DB::beginTransaction();
        $guestId = [];
        $transId = [];
        $roomUsingId = [];

        $guest = Guest::where('uuid', $req->guests['uuid']);
        $guestId = $guest;

        try {
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
                    $bookingGuest = BookingGuest::where('guest_id', $guest[0]['id']);
                    $bookingGuest->status = 1;
                    $bookingGuest->save();
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }

        return $req->all();
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
            ->where('b.id', $req->id)
            ->whereNull('bg.status');

        return $this->getListQueryBuilder($req, $query);
    }


    public function getRoomType(Request $request)
    {
        // Ngày bắt đầu và kết thúc (lấy từ request hoặc mặc định)
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $bookingId = $request->bookingId;

        // Tạo truy vấn danh sách phòng với CTE (Common Table Expression)
        $query = "
        WITH available_rooms AS (
            SELECT
                r.id AS room_id,
                r.room_number,
                r.room_type_id,
                r.status,
                ru.check_in,
                ru.check_out
            FROM
                room r
            LEFT JOIN room_using ru ON r.id = ru.room_id
            WHERE
                r.status = 1
                OR (
                    r.status = 2
                    AND (
                        ru.check_out IS NULL
                        OR (
                            ru.check_in NOT BETWEEN ? AND ?
                            OR ru.check_out NOT BETWEEN ? AND ?
                        )
                    )
                )
        )
        SELECT
            ar.room_id,
            CONCAT('Phòng ', ar.room_number) AS roomNumber,
            rt.type_name as typeName,
            ar.room_type_id
        FROM
            available_rooms ar
        JOIN bookings_details bd ON ar.room_type_id = bd.room_type_id
        LEFT JOIN room_type rt on ar.room_type_id = rt.id
        WHERE
            bd.booking_id = ?
        GROUP BY
            ar.room_id, ar.room_number, ar.room_type_id, bd.quantity, rt.type_name
        ORDER BY
            ar.room_type_id, ar.room_number
        ";

        $rooms = DB::select($query, [$startDate, $endDate, $startDate, $endDate, $bookingId]);

        $roomsCollection = collect($rooms);

        return $this->paginate($roomsCollection, $request->query('size', 20), $request->query('page', 1));
    }

    private function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
