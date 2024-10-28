<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Models\Room;
use App\Services\Api\OrderRoomService;

use Illuminate\Http\Request;

class OrderRoomController extends BaseController
{

    protected $service;

    public function __construct(OrderRoomService $service)
    {
        $this->service = $service;
    }

    public function store(Request $req) {
        // dd($req);
        $data = $this->service->handleOrderRoom($req);
        return $this->responseSuccess($data, 201);
    }

    public function update(Request $req)
    {
        // dd($req);
        $data = $this->service->updateOrderRoom($req);
        return $this->responseSuccess($data);
    }

    public function calulatorPrice(Request $req)
    {
        $data = $this->service->handleCalculatorPrice($req);
        return $this->responseSuccess($data);
    }

   // Phương thức tìm kiếm phòng trống trong OrderRoomController
public function searchRooms(Request $req)
{
    $check_in = $req->input('check_in');
    $check_out = $req->input('check_out');
    $number_of_people = $req->input('number_of_people');

    $rooms = $this->service->searchRooms($check_in, $check_out, $number_of_people);

    if (is_null($rooms)) {
        return response()->json(['message' => 'Không còn đủ phòng trống'], 404);
    }

    return $this->responseSuccess($rooms);
}


}
