<?php

namespace App\Services\Api;

use App\Services\BaseService;

use Illuminate\Http\Request;

//Models

use App\Models\Guest;
use App\Models\Transition;
use App\Models\RoomUsing;
use App\Models\RoomUsingGuest;
use App\Models\RoomUsingService;

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
            $transition = $req->transition;
            $transition['guest_id'] = $guestId->id;
            // dd($transition);
            $this->model = new Transition();
            $transId = $this->create($transition);
        }

        if (!empty($req->roomUsing)) {
            $roomUsing = $req->roomUsing;
            $trans['trans_id'] = $transId->id;
            $this->model = new RoomUsing();
            $roomUsingId = $this->create($roomUsing);
        }

        if (!empty($req->roomUsingGuest)) {
            $roomUsingGuest = $req->roomUsingGuest;
            $roomUsingGuest['guest_id'] = $guestId->id;
            $roomUsingGuest['room_using_id'] = $roomUsingId->id;
            $this->model = new RoomUsingGuest();
            $this->create($roomUsingGuest);
        }

        if (!empty($req->roomUsingService)) {
            $roomUsingService = $req->roomUsingService;
            $this->model = new RoomUsingService();
            $this->create($roomUsingService);
        }

        return $req->all();
    }

    public function handleCalculatorPrice($req)
    {
        /**
         * Request: {
         *  room_uuid: ,
         *  check_in: ,
         *  check_out: ,
         * }
         */
        $dataRoom = $this->getRoomByUuid();

        // this->model = new RoomType();
        // $roomType = $this->find($dataRoom->room_type_id)

        // Kiểm tra thời gian giữa check-in và check-out (check theo giờ hay theo ngày)
        if ($req->check_in && $req->check_out) {
            
        }
        
        // nếu theo giờ roomType->price_per_hour * số giờ = số tiền (ví dụ) công thức tự mò nhé
        // ngược lại nếu theo ngày roomType->price_per_day * số ngày = số tiền
    }

    public function getRoomByUuid()
    {
        /**
         * $this->model = new Room;
         * uuid => findByUuid -> lấy ra thông tin phòng 
         * $dataRoom = $this->findByUuid(uuid)
         * return $dataRoom;
         */
    }
}
