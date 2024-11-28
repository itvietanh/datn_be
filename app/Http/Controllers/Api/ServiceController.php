<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\ServiceService;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use illuminate\Support\Facades\DB;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    protected $service;

    public function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $columns = ['id', 'uuid', 'service_name', 'price', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $searchParams = (object) $request->only(['service_name', 'catId']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            // $query->with('hotel');
            if (isset($searchParams->catId)) {
                $query->where('service_categories_id', '=', $searchParams->catId);
            }
            if (isset($searchParams->service_name)) {
                $query->where('service_name', '=', $searchParams->service_name);
            }
        });
        return $this->getPaging($data);
    }

    public function getListService(Request $request)
    {
        $this->service->getListServiceInCat();

        $columns = ['id', 'name', 'created_at', 'updated_at'];

        $searchParams = (object) $request->only(['name', 'id']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            if (isset($searchParams->name)) {
                $query->where('name', '=', $searchParams->name);
            }
        });
        return $this->getPaging($data);
    }

    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', 'service_name as label'];

        $searchParams = (object) $req->only(['id', 'q']);

        $data = $this->service->getList($req, $fillable, function ($query) use ($searchParams) {
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name' => 'required|string',
            'service_price' => 'required|numeric',
            'hotel_id' => 'required|numeric'
        ]);

        $service = $this->service->create($data);

        return $this->responseSuccess($service, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $service = $this->service->findFirstByUuid($req->uuid, 'hotel');
        if (!$service) $this->response404();
        return $this->oneResponse($service);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $dataReq = $req->validate([
            'service_name' => 'required',
            'service_price' => 'required',
            'hotel_id' => 'required|numeric'
        ]);
        $service = $this->service->findFirstByUuid($req->uuid);
        if (!$service) $this->response404();
        $data = $this->service->update($service->id, $dataReq);
        return $this->responseSuccess($data);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $service = $this->service->findFirstByUuid($req->uuid);
        if (!$service) $this->response404();
        $this->service->delete($service->id);
        return $this->responseSuccess($service);
    }
}
