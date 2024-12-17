<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Api\BookingRoomService;
use Illuminate\Http\Request;

class BookingController extends BaseController
{

    protected $service;

    public function __construct(BookingRoomService $service)
    {
        $this->service = $service;
    }

    public function store(Request $req)
    {
        // dd($req);
        $data = $this->service->handleOrderRoom($req);
        return $this->responseSuccess($data, 201);
    }

    public function getListBookingRoom(Request $req)
    {
        $data = $this->service->getBookingList($req);
        return $this->getPaging($data);
    }

    public function getBookingRoom(Request $req)
    {
        $data = $this->service->getBookingRoom($req);
        return $this->oneResponse($data);
    }

    public function order(Request $req)
    {
        $data = $this->service->OrderRoom($req);
        return $this->oneResponse($data);
    }

    public function getListGuest(Request $req)
    {
        $data = $this->service->getGuestInBooking($req);
        return $this->getPaging($data);
    }

    public function getListRoomType(Request $req)
    {
        $data = $this->service->getRoomTypeList($req);
        return $this->getPaging($data);
    }

    public function getCombobox(Request $req)
    {
        $data = $this->service->getRoomTypeOption($req);

        return $this->getPaging($data);
    }

    public function updateRoomInRoomType(Request $req)
    {
        return $this->oneResponse($this->service->updateRoomInRt($req));
    }

    public function deleteBooking(Request $req)
    {
        return $this->oneResponse($this->service->deleteBooking($req));
    }

    public function handleConfirm(Request $req)
    {
        $booking = Booking::where('id', $req->id)->first();
        if (!$booking) {
            $this->response404();
        }
        $booking->status = 2;
        $booking->save();
        return $this->oneResponse($booking);
    }
}
