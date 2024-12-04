<?php

namespace App\Services\Api;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

//Models
use App\Models\Guest;
use App\Models\RoomType;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;
use App\Models\Room;
use App\Models\Service;
use Carbon\Carbon;
use App\Models\ServiceCategories;


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

    public function handleCalculatorPrice(Request $req)
    {
        if (!empty($req->id)) $dataRoom = $this->findFirstRoomById($req->id);

        // Lấy thông tin phòng dựa vào UUID
        if (!empty($req->room_uuid)) $dataRoom = $this->getRoomByUuid($req->room_uuid);

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
        if ($req->check_in) {
            if (strlen($req->check_in) == 14) {
                $checkIn = \DateTime::createFromFormat('YmdHis', $req->check_in);
                $checkOut = $req->check_out
                    ? \DateTime::createFromFormat('YmdHis', $req->check_out)
                    : new \DateTime();
            } else {
                $checkIn = new \DateTime($req->check_in);
                $checkOut = $req->check_out
                    ? new \DateTime($req->check_out)
                    : new \DateTime();
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

                        $remainingHoursAsNumber = $remainingHours->days * 24 + $remainingHours->h + ($remainingHours->i / 60);

                        $totalPrice += $remainingHoursAsNumber * (int) $roomType->price_per_hour;
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

    public function findFirstRoomById($id)
    {
        $this->model = new Room();
        return $this->find($id);
    }

    public function updateStatusRoomOverTime($uuid)
    {

        $data = $this->getRoomByUuid($uuid);
        $params = ['status' => 3];
        return $this->update($data->id, $params);
    }

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
            ->whereNotIn('room.room_number', function ($query) use ($check_in, $check_out) {
                $query->select('room.room_number')
                    ->from('room_using')
                    ->where(function ($q) use ($check_in, $check_out) {
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

    /** REQUEST ĐỔI PHÒNG:
     * uuid: uuid của phòng hiện tại
     * roomIdNew: id của phòng mới
     * checkIn - CheckOut: thời gian checkIn, CheckOut của phòng mới
     */

    /** Change */
    public function changeRoom($req)
    {
        $room = $this->getRoomByUuid($req->uuid);
        $room->status = 1;
        $room->save();

        $this->model = new RoomUsing();
        $roomUsing = $this->model
            ->where('room_id', $room->id)
            ->whereNull('deleted_at')->first();

        if ($roomUsing) {
            /** Update checkOut cho phòng cũ */
            $roomUsing->check_out = Carbon::now();
            if (!empty($roomUsing->room_change_fee)) {
                $roomUsing->room_change_fee = $req->transferFee;
            }

            if (!empty($roomUsing->total_amount)) {
                // Nếu có chi phí phát sinh thì cộng vào tổng tiền, không thì chỉ set tổng tiền cho trường total_amount
                $roomUsing->total_amount = $req->transferFee ? ($req->transferFee + $req->total_amount) : $req->total_amount;
            }

            $roomUsing->save();

            // Xóa mềm bản ghi room_using, sau đó tạo bản ghi room_using với id phòng mới
            $roomUsing->delete();

            /** Tạo bản ghi mới room using */
            $ruNew = $this->createRoomUsingNew($req);

            // Cập nhật lại checkIn | checkOut
            $this->updateRUGuest($req, $ruNew->id);

            /** Update trạng thái đang ở cho phòng mới */
            $this->updateRoomStatus($req->roomIdNew);
            return $roomUsing;
        }
    }

    public function updateRUGuest($req, $roomUsingId)
    {
        $this->model = new RoomUsingGuest();
        $roomUsingGuest = null;
        foreach ($req->guest as $item) {
            if (!isset($item['guestUuid'])) {
                continue;
            }

            $uuid = $item['guestUuid'];

            $guest = Guest::where('uuid', $uuid)->first();

            $rug = $this->model
                ->where('guest_id', $guest->id)->first();

            /** Update thời gian checkIn - checkOut của phòng mới */
            $dataRuGuest = [
                "room_using_id" => $roomUsingId,
                "check_in" => $this->convertLongToTimestamp($req->checkIn),
                "check_out" => $this->convertLongToTimestamp($req->checkOut)
            ];

            $roomUsingGuest = $this->update($rug->id, $dataRuGuest);
        }

        return $roomUsingGuest;
    }

    public function createRoomUsingNew($req)
    {
        $guest = $this->getRepresentaive($req);
        $transition = $this->findFirstTransition($guest->id);
        // dd($transition);
        if ($transition) {
            $this->model = new RoomUsing();
            $newRoomUsing = [
                "uuid" => str_replace('-', '', Uuid::uuid4()->toString()),
                "room_id" => $req->roomIdNew,
                "check_in" => $this->convertLongToTimestamp($req->checkIn),
                "trans_id" => $transition->id,
                "total_amount" => $req->totalAmount,
            ];
            return $this->create($newRoomUsing);
        }
        return null;
    }

    public function getRepresentaive($req)
    {
        $guestId = null;
        foreach ($req->guest as $item) {
            if (isset($item['representative']) && $item['representative'] === true) {
                $guestId = Guest::where('uuid', $item['guestUuid'])->first();
            }
        }
        $guest = Guest::where('id', $guestId->id)
            ->where('representative', true)
            ->first();
        return $guest;
    }

    public function updateRoomStatus($id)
    {
        $this->model = new Room();
        $room = $this->model->find($id);
        $room->status = 2;
        $room->save();
    }

    public function findFirstTransition($guestId)
    {
        $transition = Transition::where('guest_id', $guestId)->first();
        return $transition;
    }

    /**
     * Tìm kiếm phòng trống
     */
    public function searchRoomsAvailable($req)
    {
        $check_in = $this->convertLongToTimestamp($req->checkIn);
        $check_out = $this->convertLongToTimestamp($req->checkOut);

        $data = $this->getAvailableRoomsQuery($req);

        $data->getCollection()->transform(function ($value) use ($check_in, $check_out) {
            // Tính giá phòng cho từng phòng
            $priceData = $this->calculateRoomPrice($value->id, $check_in, $check_out);

            // Gộp priceData vào từng phần tử dữ liệu
            return (object) array_merge((array) $value, (array) $priceData);
        });

        return $data;
    }


    private function getAvailableRoomsQuery($req)
    {
        $this->model = new RoomType();
        $columns = [
            'rt.id',
            'rt.type_name AS typeName',
            'rt.number_of_people AS numberOfPeople',
            'rt.price_per_hour AS pricePerHour',
            'rt.price_overtime AS priceOvertime',
            'rt.price_per_day AS pricePerDay',
            DB::raw('COUNT(CASE WHEN r.status = 1 THEN r.id END) AS phongTrong'),
            DB::raw('COUNT(CASE WHEN r.status = 2 THEN r.id END) AS dangO'),
            DB::raw('COUNT(r.id) AS totalRooms'),
            DB::raw('SUM(rt.number_of_people) AS totalCapacity'),
            DB::raw('SUM(rt.number_of_people) - COALESCE(SUM(b.guest_count), 0) AS availableCapacity')
        ];

        // Chuyển đổi timestamp nếu cần
        $req->checkIn = $this->convertLongToTimestamp($req->checkIn);
        $req->checkOut = $this->convertLongToTimestamp($req->checkOut);

        $data = $this->getList($req, $columns, function ($query) use ($req) {
            // Join các bảng
            $query->from('room_type as rt')
                ->join('room as r', 'r.room_type_id', '=', 'rt.id')
                ->leftJoin('bookings as b', function ($join) use ($req) {
                    $join->on('b.room_type_id', '=', 'rt.id')
                        ->where('b.check_in', '>=', $req->checkIn)
                        ->where('b.check_out', '<=', $req->checkOut);
                });
            // Thêm điều kiện lọc
            $query->groupBy('rt.id', 'rt.type_name', 'rt.number_of_people', 'rt.price_per_hour', 'rt.price_overtime', 'rt.price_per_day')
                ->havingRaw('SUM(rt.number_of_people) - COALESCE(SUM(b.guest_count), 0) >= ?', [$req->totalGuest]);
        });
        return $data;
    }

    public function calculateRoomPrice($roomTypeId, $checkInParam, $checkOutParam)
    {
        $this->model = new RoomType();
        $roomType = $this->find($roomTypeId);

        if (!$roomType) {
            return response()->json([
                'error' => 'NOT FOUND ROOM TYPE'
            ], 404);
        }

        // Kiểm tra thời gian giữa check-in và check-out
        if ($checkInParam) {
            if (strlen($checkInParam) == 14) {
                $checkIn = \DateTime::createFromFormat('YmdHis', $checkInParam);
                $checkOut = $checkOutParam
                    ? \DateTime::createFromFormat('YmdHis', $checkOutParam)
                    : new \DateTime();
            } else {
                $checkIn = new \DateTime($checkInParam);
                $checkOut = $checkOutParam
                    ? new \DateTime($checkOutParam)
                    : new \DateTime();
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

                        $remainingHoursAsNumber = $remainingHours->days * 24 + $remainingHours->h + ($remainingHours->i / 60);

                        $totalPrice += $remainingHoursAsNumber * (int) $roomType->price_per_hour;
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
                    'final_price' => $finalPrice,
                    'check_in' => $checkInParam,
                    'check_out' => $checkOutParam,
                ];

                return $data;
            }
        }
    }
}
