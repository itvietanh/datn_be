<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

// Request
use App\Http\Requests\HotelRequest;

//Model
use App\Models\Hotel;

// Service
use App\Services\Api\HotelService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HotelController extends BaseController
{

    protected $service;

    public function __construct(HotelService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $fillable = ['uuid', 'name', 'address', 'star_rating', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $searchParams = $req->only(['page', 'size', 'name', 'address']);

        $data = $this->service->getList($req, $fillable);

        

        return $this->getPaging($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HotelRequest $req)
    {
        $params = $req->all();
        $hotel = $this->service->create($params);
        return $this->responseSuccess($hotel, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $data = $this->service->findFirstByUuid($req->uuid);
        return $this->oneResponse($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HotelRequest $req)
    {
        $hotel = $this->service->findFirstByUuid($req->uuid);
        if (!$hotel) {
            return $this->response404(); 
        }
        $data = $this->service->update($hotel->id, $req->all());
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $hotel = $this->service->findFirstByUuid($req->uuid);
        if (!$hotel) {
            return $this->response404(); 
        }
        $data = $this->service->delete($hotel->id);
        return $this->responseSuccess($data);
    }
}
