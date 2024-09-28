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

    public function index()
    {
        // return RoomUsingService::all();
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
