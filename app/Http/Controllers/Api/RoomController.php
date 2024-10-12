<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\Api\RoomService;

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
        $data = $this->service->getList($req, $column, function($query) use ($searchParams) {
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

        $searchParams = (object) $req->only(['hotel_id', 'q']);

        $data = $this->service->getList($req, $fillable, function($query) use ($searchParams) {
            if (!empty($searchParams->hotel_id)) {
                $query->where('hotel_id', '=', $searchParams->hotel_id);
            }
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
}
