<?php

namespace App\Services\Api;

use App\Services\BaseService;

use Illuminate\Http\Request;

//Models

use App\Models\Guest;
use App\Models\RoomType;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;
use App\Models\Room;


// Enum

use App\RoomStatusEnum;

class OrderRoomService extends BaseService
{
    public function __construct() {}

    public function handleOrderRoom(Request $req)
    {
        $guestId = [];
        $transId = [];
        $roomUsingId = [];

        if (!empty($req->guest)) {
            $guest = $req->guest;
            $this->model = new Guest();
            $guestId = $this->create($guest);
        }

        if (!empty($req->transition)) {
            $transitionDateTime = $this->convertLongToTimestamp($req->transition['transition_date']);
            $transition = $req->transition;
            $transition['guest_id'] = $guestId->id;
            $transition['transition_date'] = $transitionDateTime;
            $this->model = new Transition();
            $transId = $this->create($transition);
        }

        if (!empty($req->roomUsing)) {
            $checkIn = $this->convertLongToTimestamp($req->roomUsing['check_in']);
            $checkOut = $this->convertLongToTimestamp($req->roomUsing['check_out']);
            $roomUsing = $req->roomUsing;
            $roomUsing['trans_id'] = $transId->id;
            $roomUsing['check_in'] = $checkIn;
            $roomUsing['check_out'] = $checkOut;
            // dd($roomUsing);
            $this->model = new RoomUsing();
            $roomUsingId = $this->create($roomUsing);
        }

        if (!empty($req->roomUsingGuest)) {
            $checkIn = $this->convertLongToTimestamp($req->roomUsingGuest['check_in']);
            $checkOut = $this->convertLongToTimestamp($req->roomUsingGuest['check_out']);
            $roomUsingGuest = $req->roomUsingGuest;
            $roomUsingGuest['guest_id'] = $guestId->id;
            $roomUsingGuest['room_using_id'] = $roomUsingId->id;
            $roomUsingGuest['check_in'] = $checkIn;
            $roomUsingGuest['check_out'] = $checkOut;
            $this->model = new RoomUsingGuest();
            $this->create($roomUsingGuest);
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
            $checkIn = \DateTime::createFromFormat('YmdHis', $req->check_in);
            $checkOut = \DateTime::createFromFormat('YmdHis', $req->check_out);

            if ($checkIn && $checkOut) {
                // Tính thời gian chênh lệch giữa check-in và check-out theo giờ
                $interval = $checkIn->diff($checkOut);
                $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60); // Chuyển đổi sang giờ

                // Tính giá tiền dựa trên giờ
                if ($hours < 24) {
                    $totalPrice = $roomType->price_per_hour * $hours;
                } else {
                    // Nếu thời gian >= 24 giờ, tính số ngày và số giờ dư
                    $days = floor($hours / 24); // Số ngày
                    $remainingHours = $hours - ($days * 24); // Số giờ còn lại

                    // Tính tổng tiền: tiền cho số ngày + tiền cho số giờ dư
                    $totalPrice = ($roomType->price_per_day * $days) + ($roomType->price_per_hour * $remainingHours);
                }

                // Tính mã số thuế (VAT)
                $vat = $roomType->vat;
                $tax = ($totalPrice * $vat) / 100;

                // Tổng tiền sau thuế
                $finalPrice = $totalPrice + $tax;
                $totalPrice = round($totalPrice, 2);
                $tax = round($tax, 2);
                $finalPrice = round($finalPrice, 2);

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
        // Giả sử bạn đã có Room model với hàm findByUuid
        $this->model = new Room();
        return $this->findFirstByUuid($uuid);
    }
}
