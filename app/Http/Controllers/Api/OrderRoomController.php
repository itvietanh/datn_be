<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Room;
use App\Services\Api\OrderRoomService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderRoomController extends BaseController
{
    protected $service;

    public function __construct(OrderRoomService $service)
    {
        $this->service = $service;
    }

    public function store(Request $req)
    {
        // dd($req);
        $data = $this->service->handleOrderRoom($req);
        return $this->responseSuccess($data, 201);
    }

    public function calulatorPrice(Request $req)
    {
        $data = $this->service->handleCalculatorPrice($req);
        return $this->responseSuccess($data);
    }

    public function handleOverTime(Request $req)
    {
        $data = $this->service->updateStatusRoomOverTime($req->uuid);
        return $this->oneResponse($data->uuid);
    }

    public function searchRooms(Request $req)
    {
        $check_in = $req->input('check_in');
        $check_out = $req->input('check_out');
        $number_of_people = $req->input('number_of_people');

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
            return response()->json(['message' => 'Không còn đủ phòng trống'], 404);
        }

        $rooms->transform(function ($item) {
            $item->guests = json_decode($item->guests);
            return $item;
        });

        return response()->json($rooms);
    }

    /**
     * 
     */
}
