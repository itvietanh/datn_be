<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Api\FloorService;

class FloorController extends BaseController
{
    protected $service;
    public function __construct(FloorService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $columns = [
            'floor.id',
            'floor.uuid',
            'floor.hotel_id as hotelId',
            'floor.floor_number as floorNumber',
            'floor.created_at as createdAt',
            'floor.updated_at as createdBy',
        ];

        $searchParams = (object) $request->only(['hotel_id', 'floor_number']);

        $data = $this->service->getListByWith($request, $columns, function ($query) use ($searchParams) {
            $query->join('hotel', 'floor.hotel_id', '=', 'hotel.id');
    
            if (isset($searchParams->hotel_id)) {
                $query->where('floor.hotel_id', '=', $searchParams->hotel_id);
            }
            if (isset($searchParams->floor_number)) {
                $query->where('floor.floor_number', '=', $searchParams->floor_number);
            }
        }, ['rooms:id, room_number as roomNumber, status, floor_id']);

        return $this->getPaging($data);
    }

    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', DB::raw("CONCAT('Tầng ', floor_number) as label")];

        $searchParams = (object) $req->only(['id', 'q']);

        $data = $this->service->getList($req, $fillable, function($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->id)) {
                $query->where('id', '=', $searchParams->id);
            }
        });

        return $this->getPaging($data);
    }


    /**
     * Show the form for creating a new resource.
     */
    // public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataReq = $request->validate([
            'hotel_id' => 'required|integer',
            'floor_number' => 'required|integer'
        ]);

        $floor = $this->service->create($dataReq);

        return $this->responseSuccess($floor, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $floor = $this->service->findFirstByUuid($req->uuid);
        if (!$floor) $this->response404();
        return $this->oneResponse($floor);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $dataReq = $req->validate([
            'hotel_id' => 'required|integer',
            'floor_number' => 'required|integer'
        ]);
        $floor = $this->service->findFirstByUuid($req->uuid);
        if (!$floor) $this->response404();
        $data = $this->service->update($floor->id, $dataReq);
        return $this->responseSuccess($data);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $floor = $this->service->findFirstByUuid($req->uuid);
        if (!$floor) $this->response404();
        $this->service->delete($floor->id);
        return $this->responseSuccess($floor);
    }
}
