<?php

namespace App\Services\Api;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

//Models

use App\Models\Guest;
use App\Models\RoomType;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;
use App\Models\Room;
use Carbon\Carbon;


// Enum

use App\RoomStatusEnum;
// use Illuminate\Container\Attributes\DB;

class OrderRoomService extends BaseService
{
    public function __construct() {}

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
            $checkOut = $this->convertLongToTimestamp($req->roomUsingGuest['check_out']);
            $roomUsingGuest = $req->roomUsingGuest;
            // dd($guestId);
            foreach ($guestId as $value) {
                $roomUsingGuest['guest_id'] = $value->id;
                $roomUsingGuest['room_using_id'] = $roomUsingId->id;
                $roomUsingGuest['check_in'] = $checkIn;
                $roomUsingGuest['check_out'] = $checkOut;
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

    public function handleCalculatorPrice(Request $req)
    {
        // Lấy thông tin phòng dựa vào UUID
        $dataRoom = $this->getRoomByUuid($req->room_uuid);

        // Kiểm tra phòng có tồn tại không
        if (!$dataRoom) {
            return response()->json([
                'error' => 'NOT FOUND ROOM'
            ], 404);
        }

        // Lấy loại phòng dựa vào room_type_id
        $this->model = new RoomType();
        $roomType = $this->find($dataRoom->room_type_id);

        if (!$roomType) {
            return response()->json([
                'error' => 'NOT FOUND ROOM TYPE'
            ], 404);
        }

        // Kiểm tra thời gian giữa check-in và check-out
        if ($req->check_in && $req->check_out) {
            if (strlen($req->check_in) == 14) {
                $checkIn = \DateTime::createFromFormat('YmdHis', $req->check_in);
                $checkOut = \DateTime::createFromFormat('YmdHis', $req->check_out);
            } else {
                $checkIn = new \DateTime($req->check_in);
                $checkOut = new \DateTime($req->check_out);
            }

            if ($checkIn && $checkOut) {
                // Tính thời gian chênh lệch giữa check-in và check-out theo giờ
                $interval = $checkIn->diff($checkOut);
                $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60); // Chuyển đổi sang giờ

                // Tính giá tiền dựa trên giờ
                if ($hours < 24) {

                    $totalPrice = $roomType->price_per_hour * $hours;

                    $timeNow = Carbon::now();
                    if ($timeNow > $checkOut) {
                        $remainingHours = $checkOut->diff($timeNow);
                        $totalPrice = $totalPrice + ($remainingHours * $roomType->price_per_hour);
                    }
                } else {
                    // Nếu thời gian >= 24 giờ, tính số ngày và số giờ dư
                    $days = $hours / 24; // Số ngày
                    $remainingDay = $hours - ($days * 24); // Số giờ còn lại

                    // Tính tổng tiền: tiền cho số ngày + tiền cho số giờ dư
                    $totalPrice = $roomType->price_per_day * $days;
                }

                // Tính mã số thuế (VAT)
                $vat = $roomType->vat;
                $tax = ($totalPrice * $vat) / 100;

                // Tổng tiền sau thuế
                $finalPrice = $totalPrice + $tax;

                $data = [
                    'total_price' => $totalPrice,
                    'vat' => $tax,
                    'final_price' => $finalPrice, // Tổng tiền bao gồm thuế
                    'check_in' => $req->check_in,
                    'check_out' => $req->check_out,
                ];

                // Trả về kết quả tính toán
                return $data;
            }
        }
    }

    public function getRoomByUuid($uuid)
    {
        $this->model = new Room();
        return $this->findFirstByUuid($uuid);
    }

    public function updateStatusRoomOverTime($uuid)
    {

        $data = $this->getRoomByUuid($uuid);
        $params = ['status' => 3];
        return $this->update($data->id, $params);
    }
<<<<<<< HEAD
    public function searchRooms($check_in, $check_out, $number_of_people)
    {
        $query = DB::table('room')
            ->select(
                'room.id',
                'room.uuid',
                'room.room_number AS roomNumber',
                'room.status',
                'room_type.type_name AS typeName',
                'room_type.number_of_people AS numberOfPeople',
                'room_using.check_in AS checkIn',
                'room_using.check_out AS checkOut',
                DB::raw("COALESCE(
                    jsonb_agg(
                        DISTINCT jsonb_build_object(
                            'uuid', guest.uuid,
                            'name', guest.name,
                            'phoneNumber', guest.phone_number
                        )
                    ) FILTER (WHERE guest.id IS NOT NULL),
                    '[]'::jsonb
                ) AS guests")
            )
            ->join('room_type', 'room.room_type_id', '=', 'room_type.id')
            ->leftJoin('room_using', 'room.id', '=', 'room_using.room_id')
            ->leftJoin('room_using_guest', 'room_using.id', '=', 'room_using_guest.room_using_id')
            ->leftJoin('guest', 'room_using_guest.guest_id', '=', 'guest.id')
            ->where('room.status', '=', 1)
            ->where('room_type.number_of_people', '>=', $number_of_people)
            ->whereNotIn('room.room_number', function($query) use ($check_in, $check_out) {
                $query->select('room.room_number')
                      ->from('room_using')
                      ->where(function($q) use ($check_in, $check_out) {
                          $q->where('room_using.check_in', '<', $check_out)
                            ->where('room_using.check_out', '>', $check_in);
                      });
            })
            ->groupBy('room.id', 'room.uuid', 'room.room_number', 'room.status', 'room_type.type_name', 'room_type.number_of_people', 'room_using.check_in', 'room_using.check_out')
            ->orderBy('room.room_number', 'ASC');

        $rooms = $query->get();

        if ($rooms->isEmpty()) {
            return null;
        }

        $rooms->transform(function ($item) {
            $item->guests = json_decode($item->guests);
            return $item;
        });

        return $rooms;
    }
=======

    public function roomChange($req) {}
>>>>>>> e6cbb2b33e5714d436f0a92e65b8d14886ddf074
}
