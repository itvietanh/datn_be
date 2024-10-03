<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\RoomUsingService;

class RoomUsingServiceController extends BaseController
{
    
    protected $service;

    public function __construct(RoomUsingService $service) {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $columns = ['uuid', 'room_using_id', 'service_id', 'service_using_date','created_at', 'updated_at', 'created_by', 'updated_by'];
        $searchParams = (object) $request->only(['room_using_id', 'service_id', 'service_using_date']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            if (isset($searchParams->room_using_id)) {
                $query->where('room_using_id', '=', $searchParams->room_using_id);
            }
            if (isset($searchParams->service_id)) {
                $query->where('service_id', '=', $searchParams->service_id);
            }
            if (isset($searchParams->service_using_date)) {
                $query->where('service_using_date', '=', $searchParams->service_using_date);
            }
        });
        return $this->getPaging($data);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_using_id' => 'required|integer',
            'service_id' => 'required|integer',
            'service_using_date' => 'required|date',
        ]);

        $service = $this->service->create($data);
        return $this->responseSuccess($service, 201);
    }

    public function show(Request $req)
    {
        $roomUsingServ = $this->service->findFirstByUuid($req->uuid);
        return $this->oneResponse($roomUsingServ);
    }


    public function update(Request $req, $uuid)
    {
        $roomUsingServ = $this->service->findFirstByUuid($req->uuid);
        if (!$roomUsingServ) {
            return $this->response404(); 
        }
        $data = $this->service->update($roomUsingServ->id, $req->all());
        return $this->responseSuccess($data);
    }


public function destroy(Request $req)
{
    $roomUsingServ = $this->service->findFirstByUuid($req->uuid);
    if (!$roomUsingServ) {
        return $this->response404(); 
    }
    $data = $this->service->delete($roomUsingServ->id);
    return $this->responseSuccess($data);
}


}
