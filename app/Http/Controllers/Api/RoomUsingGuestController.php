<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Services\Api\RoomUsingGuestService;
use Illuminate\Http\Request;

class RoomUsingGuestController extends BaseController
{
    protected $service;

    public function __construct(RoomUsingGuestService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $column = ['uuid', 'guest_id', 'room_using_id', 'check_in', 'check_out', 'created_at', 'updated_at', 'created_by', 'updated_by'];
        $guests = $this->service->getList($req, $column);
        return $this->getPaging($guests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'guest_id' => 'required|integer',
            'room_using_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);
        $guest = $this->service->create($validatedData);
        return $this->responseSuccess($guest, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        // $guest = $this->service::find($uuid);
        // if (!$guest) {
        //     return $this->response404('Guest not found.');
        // }
        // return $this->responseSuccess($guest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // // Tìm guest bằng uuid
        $guest = $this->service->findFirstByUuid($request->uuid);
        if (!$guest) {
            return $this->response404();
        }

        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'guest_id' => 'required|integer',
            'room_using_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        // Cập nhật thông tin guest
        $data = $this->service->update($guest->id, $validatedData);
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
}
