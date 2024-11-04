<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\HomeHotelService;

class HomeHotelController extends BaseController
{

    protected $service;
    public function __construct(HomeHotelService $service)
    {
        $this->service = $service;
    }

    public function getRoomUsing(Request $req)
    {
        // dd($req);
        $data = $this->service->handleGetRoomUsing($req);
        return $this->oneResponse($data);
    }

    public function getRoomUsingGuest(Request $req)
    {
        // dd($req);
        $data = $this->service->handleGetRoomUsingGuest($req);
        return $this->getPaging($data);
    }

    public function addGuestRoomUsing(Request $req)
    {
        $data = $this->service->handleAddGuestInRoomUsing($req);
        return $this->responseSuccess($data, 201);
    }
}
