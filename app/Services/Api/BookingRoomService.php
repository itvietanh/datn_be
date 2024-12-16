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
use App\Models\RoomType;
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
            $roomUsingId = [];

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

                foreach ($guestId as $value) {
                    $this->model = new BookingGuest();
                    $bookingGuestData = [
                        'booking_id' => $bookingsId->id,
                        'guest_id' => $value->id,
                    ];
                    $this->create($bookingGuestData);
                }


                $bookingDetail = $req->bookingDetail;
                foreach ($bookingDetail as $detail) {
                    $detail['booking_id'] = $bookingsId->id;
                    $detail['room_type_id'] = $detail['room_type_id'];
                    $detail['quantity'] = $detail['quantity'];
                    $this->model = new BookingDetail();
                    $this->create($bookingDetail);
                }
            }

            if (!empty($req->roomUsing)) {
                $checkIn = $this->convertLongToTimestamp($req->roomUsing['check_in']);
                $roomUsing = $req->roomUsing;

                foreach ($bookingDetail as $detail) {
                    $roomTypeId = $detail['room_type_id'];
                    $quantity = $detail['quantity'];
                    for ($i = 0; $i < $quantity; $i++) {
                        $roomUsing['uuid'] = str_replace('-', '', Uuid::uuid4()->toString());
                        $roomUsing['booking_id'] = $bookingsId->id;
                        $roomUsing['check_in'] = $checkIn;
                        $roomUsing['room_type_id'] = $roomTypeId;
                        $this->model = new RoomUsing();
                        $roomUsingId[] = $this->create($roomUsing);
                    }
                }
            }

            // if (!empty($req->roomUsingGuest)) {
            //     $checkIn = $this->convertLongToTimestamp($req->roomUsingGuest['check_in']);
            //     $checkOut = null;
            //     if ($req->roomUsingGuest['check_out']) {
            //         $checkOut = $this->convertLongToTimestamp($req->roomUsingGuest['check_out']);
            //     }
            //     $roomUsingGuest = $req->roomUsingGuest;
            //     foreach ($roomUsingId as $ruValue) {
            //         $roomUsingGuest['uuid'] = str_replace('-', '', Uuid::uuid4()->toString());
            //         $roomUsingGuest['room_using_id'] = $ruValue->id;
            //         $roomUsingGuest['check_in'] = $checkIn;
            //         if ($checkOut) {
            //             $roomUsingGuest['check_out'] = $checkOut;
            //         }
            //         $this->model = new RoomUsingGuest();
            //         $this->create($roomUsingGuest);
            //     }
            // }

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

            return response()->json(['message' => 'Success', 'data' => $req->all()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage());
        }
    }

    public function OrderRoom(Request $req)
    {
        DB::beginTransaction();
        try {
            if (!empty($req->roomUsingGuest)) {
                $checkIn = $this->convertLongToTimestamp($req->roomUsingGuest['check_in']);
                $checkOut = null;
                if ($req->roomUsingGuest['check_out']) {
                    $checkOut = $this->convertLongToTimestamp($req->roomUsingGuest['check_out']);
                }

                $roomUsingGuest = $req->roomUsingGuest;
                $guest = Guest::where('uuid', $roomUsingGuest['guestUuid'])->first();
                $roomUsingGuest['uuid'] = str_replace('-', '', Uuid::uuid4()->toString());
                $roomUsingGuest['check_in'] = $checkIn;
                $roomUsingGuest['guest_id'] = $guest->id;
                if ($checkOut) {
                    $roomUsingGuest['check_out'] = $checkOut;
                }
                $this->model = new RoomUsingGuest();
                $this->create($roomUsingGuest);

                $bookingGuest = BookingGuest::where('guest_id', $guest->id)->first();
                if ($bookingGuest) {
                    $bookingGuest->status = 1;
                    $bookingGuest->save();
                } else {
                    return $this->responseError("Không tìm thấy khách", 404);
                }
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
                'g.name as guestName',
                'b.group_name as groupName',
                'b.order_date as orderDate',
                'b.check_in as checkIn',
                'b.check_out as checkOut',
                'b.status',
                DB::raw("CONCAT(b.room_quantity, ' Phòng') as roomQuantity"),
                DB::raw("CONCAT(b.guest_count, ' Khách') as guestCount")
            )
            ->leftJoin('guest as g', 'g.id', '=', 'b.representative_id');

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

    public function getRoomTypeList(Request $request)
    {
        $bookingId = $request->bookingId;
        $query = DB::table('bookings as b')
            ->leftJoin('room_using as ru', 'b.id', '=', 'ru.booking_id')
            ->leftJoin('room_type as rt', 'rt.id', '=', 'ru.room_type_id')
            ->leftJoin('room as r', 'r.id', '=', 'ru.room_id')
            ->select(
                'ru.uuid as ruUuid',
                'ru.id as ruId',
                'rt.id',
                'rt.number_of_people as numberOfPeople',
                'rt.type_name as typeName',
                DB::raw("CASE WHEN ru.room_id IS NULL THEN 'Trống' ELSE CONCAT('Phòng ', r.room_number) END as roomNumber")
            )
            ->where('b.id', $bookingId);

        $result = $this->getListQueryBuilder($request, $query);

        return $result;
    }

    public function updateRoomInRt(Request $req)
    {
        // dd($req);
        $ruId = $req->ruId;
        $roomId = $req->roomId;

        $this->model = new RoomUsing();
        $ru = $this->find($ruId);
        $ru->room_id = $roomId;
        $ru->save();

        $this->model = new Room();
        $room = $this->find($roomId);
        $room->status = RoomStatusEnum::DANG_O->value;
        $room->save();
        return $ru;
    }


    public function getRoomTypeOption(Request $req)
    {
        $fillable = ['id as value', 'type_name as label'];

        $searchParams = (object) $req->only(['id', 'q', 'idStr']);

        $this->model = new RoomType();

        $data = $this->getList($req, $fillable, function ($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('type_name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->id)) {
                $query->where('id', '=', $searchParams->id);
            }

            if (!empty($searchParams->idStr)) {
                $idArray = array_filter(
                    explode(',', $searchParams->idStr),
                    fn($id) => is_numeric($id)
                );

                if (!empty($idArray)) {
                    $query->whereIn('id', $idArray);
                }
            }
        });

        return $data;
    }
}
