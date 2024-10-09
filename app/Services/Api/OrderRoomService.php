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

    public function handleOrderRoom(Request $req) {
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
}
