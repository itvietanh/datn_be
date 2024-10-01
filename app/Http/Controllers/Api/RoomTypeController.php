<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\Services\Api\RoomTypeService;

class RoomTypeController extends BaseController
{
    protected $service;

    public function __construct(RoomTypeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $column = ['uuid', 'hotel_id', 'type_name', 'type_price', 'description','created_at', 'updated_at', 'created_by', 'updated_by'];
        $searchParams = (object) $req->only(['hotel_id', 'type_name']);
        $data = $this->service->getList($req, $column, function ($query) use ($searchParams) {
            if (isset($searchParams->hotel_id)) {
                $query->where('room_type.hotel_id', '=', $searchParams->hotel_id);
            }
            if (isset($searchParams->type_name)) {
                $query->where('room_type.type_name', '=', $searchParams->floor_number);
            }
        });
        return $this->getPaging($data);
    }

    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', 'type_name as label'];

        $searchParams = (object) $req->only(['id', 'q']);

        $data = $this->service->getList($req, $fillable, function($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('type_name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->id)) {
                $query->where('id', '=', $searchParams->id);
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
        $roomType = $this->service->findFirstByUuid($req->uuid);
        if (!$roomType) $this->response404();
        return $this->oneResponse($roomType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $dataReq = $req->all();
        $roomType = $this->service->findFirstByUuid($req->uuid);
        if (!$roomType) $this->response404();
        $data = $this->service->update($roomType->id, $dataReq);
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $roomType = $this->service->findFirstByUuid($req->uuid);
        if (!$roomType) $this->response404();
        $this->service->delete($roomType->id);
        return $this->responseSuccess($roomType);
    }
}
