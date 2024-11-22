<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\RoomUsing;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Services\Api\RoomUsingService;
use App\Services\BaseService;
use Carbon\Carbon;

class RoomUsingServiceController extends BaseController
{

    protected $service;

    public function __construct(RoomUsingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $columns = ['uuid', 'room_using_id', 'service_id', 'service_using_date', 'created_at', 'updated_at', 'created_by', 'updated_by'];
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
        $data = $request->all();
        $data['service_using_date'] = Carbon::now();
        $ruId = RoomUsing::where('uuid', $data['room_using_id'])->first();
        $data['room_using_id'] = $ruId->id;
        if (isset($data['service_id']) && is_array($data['service_id'])) {
            foreach ($data['service_id'] as $serviceId) {
                $recordData = [
                    'uuid' => $data['uuid'],
                    'room_using_id' => $data['room_using_id'],
                    'service_id' => $serviceId,
                    'service_using_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $service = $this->service->create($recordData);
            }
        }
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



    public function calculateServiceFee(Request $request)
    {
        $request->validate([
            'room_using_id' => 'required|integer',
            'services' => 'required|array',
            'services.*.service_id' => 'required|integer',
            'services.*.total' => 'required|integer',
        ]);

        $totalFee = 0;
        foreach ($request->services as $serviceData) {
            $service = Service::find($serviceData['service_id']);

            if (!$service) {
                return $this->response404();
            }
            $serviceFee = $service->service_price * $serviceData['total'];
            $totalFee += $serviceFee;
        }
        return $this->responseSuccess([
            'total_fee' => $totalFee,
        ]);
    }
}
