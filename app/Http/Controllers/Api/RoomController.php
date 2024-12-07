<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomUsing;
use App\Models\RoomUsingService;
use App\Models\Service;
use App\Models\Transition;
use App\RoomStatusEnum;
use App\PaymentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\Api\RoomService;
use Carbon\Carbon;

class RoomController extends BaseController
{

    protected $service;

    public function __construct(RoomService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $column = ['uuid', 'hotel_id', 'floor_id', 'room_type_id', 'room_number', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'];
        $searchParams = (object) $req->only(['hotel_id', 'room_number']);
        $data = $this->service->getList($req, $column, function ($query) use ($searchParams) {
            if (isset($searchParams->hotel_id)) {
                $query->where('hotel_id', '=', $searchParams->hotel_id);
            }
            if (isset($searchParams->room_number)) {
                $query->where('room_number', '=', $searchParams->floor_number);
            }
        });
        return $this->getPaging($data);
    }

    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', DB::raw("CONCAT('Phòng ', room_number) as label")];

        $searchParams = (object) $req->only(['hotel_id', 'q', 'room_type_id']);

        $data = $this->service->getList($req, $fillable, function ($query) use ($searchParams) {
            if (!empty($searchParams->hotel_id)) {
                $query->where('hotel_id', '=', $searchParams->hotel_id);
            }

            if (!empty($searchParams->room_type_id)) {
                $query->where('room_type_id', '=', $searchParams->room_type_id);
            }
            $query->where('room.status', '=', RoomStatusEnum::PHONG_TRONG->value); // Lấy phòng đang trống
        });

        return $this->getPaging($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $dataReq = $request->all();
        $data = $this->service->create($dataReq);
        return $this->responseSuccess($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $room = $this->service->findFirstByUuid($req->uuid);
        if (!$room) $this->response404();
        return $this->oneResponse($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $dataReq = $req->all();
        $room = $this->service->findFirstByUuid($req->uuid);
        if (!$room) $this->response404();
        $data = $this->service->update($room->id, $dataReq);
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $room = $this->service->findFirstByUuid($req->uuid);
        if (!$room) $this->response404();
        $this->service->delete($room->id);
        return $this->responseSuccess($room);
    }
    public function handleOutRoom(Request $req)
    {
        $params = (object) $req->all();
        $room = Room::where('uuid', $params->uuid)->first();
        $ru = RoomUsing::where('room_id', $room->id)->first();
        $trans = Transition::where('id', $ru->trans_id)->first();
        $rus = RoomUsingService::where('room_using_id', $ru->id)->first();

        if ($ru->id) {
            $rus = RoomUsingService::where('room_using_id', $ru->id)->first();
        }
        if ($rus->service_id) {
            $service = Service::where('id', $rus->service_id)->get();
            if ($service) {
                $totalPriceService = 0;
                foreach ($service as $value) {
                    $totalPriceService += $value->price;
                }
                $params->total_amount = $params->total_amount + $totalPriceService;
            }
        }
        if ($room) {
            $room->status = RoomStatusEnum::CAN_DON->value;
            $ru->total_amount = $params->total_amount;
            $trans->payment_status = PaymentStatusEnum::DA_THANH_TOAN->value;
            $rus->status = PaymentStatusEnum::DA_THANH_TOAN->value;
            $rus->save();
            $trans->save();
            $ru->save();
            $ru->delete();
            $room->save();
            return $this->responseSuccess($room->status, 200);
        }
        return $this->responseError();
    }

    public function handleChangeRoomStatus(Request $req)
    {
        $params = (object) $req->all();
        $room = Room::where('uuid', $params->uuid)->first();
        if ($room) {
            $room->status = RoomStatusEnum::PHONG_TRONG->value;
            $room->save();
        } else {
            return $this->response404();
        }
        return $this->responseSuccess($room->status);
    }
}
