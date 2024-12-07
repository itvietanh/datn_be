<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Services\Api\BookingRoomService;
use Illuminate\Http\Request;

class BookingController extends BaseController
{

    protected $service;

    public function __construct(BookingRoomService $service)
    {
        $this->service = $service;
    }

    public function getListBookingRoom(Request $req)
    {
        $data = $this->service->getBookingList($req);
        return $this->getPaging($data);
    }
}
