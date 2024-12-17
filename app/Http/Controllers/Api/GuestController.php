<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\BaseController;

// Request
use App\Http\Requests\GuestRequest;
use App\Models\Guest;
// Service
use App\Services\Api\GuestService;
use Illuminate\Support\Facades\DB;

class GuestController extends BaseController

{
    protected $service;
    public function __construct(GuestService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $columns = [
            'guest.uuid',
            'guest.name',
            'guest.contact_details',
            'guest.id_number',
            'guest.passport_number',
            'guest.created_at',
            'guest.updated_at',
            'guest.deleted_at',
            'guest.created_by',
            'guest.updated_by',
            'guest.province_id',
            'guest.district_id',
            'guest.ward_id',
            'guest.phone_number',
            'guest.representative',
            'guest.gender',
            'guest.birth_date',
            'guest.nat_id'
        ];
        $searchParams = (object) $request->only(['name', 'id_number']);
        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            if (!empty($searchParams->name)) {
                $query->where('guest.name', 'like', '%' . $searchParams->name . '%');
            }
            if (!empty($searchParams->id_number)) {
                $query->where('guest.id_number', '=', $searchParams->id_number);
            }
            $query->with(['province', 'district', 'ward']);
        });
        return $this->getPaging($data);
    }
    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', "name as label"];

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


    public function store(GuestRequest $request)
    {
        $params = $request->all();
        $guest = $this->service->create($params);
        return $this->responseSuccess($guest, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $data = $this->service->findFirstByUuid($request->uuid);
        return $this->oneResponse($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GuestRequest $request)
    {
        $guest = $this->service->findFirstByUuid($request->uuid);
        // dd($guest);
        if (!$guest) {
            return $this->response404();
        }
        $data = $this->service->update($guest->id, $request->all());
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $guest = $this->service->findFirstByUuid($request->uuid);
        if (!$guest) {
            return $this->response404();
        }
        $data = $this->service->delete($guest->id);
        return $this->responseSuccess($data);
    }

    public function insertRoomUsingGuest(Request $request)
    {
        $data = $this->service->findFirstByUuid($request->uuid);
        $params = $request->all();
        
        $guest = $this->service->create($params);
    }
}
