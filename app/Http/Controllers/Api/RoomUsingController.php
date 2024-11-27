<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\RoomUsingService;

use App\Http\Controllers\BaseController;
use App\Models\RoomUsing;

class RoomUsingController extends BaseController
{
    protected $service;
    public function __construct(RoomUsingService $service){
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */


    public function index(Request $req)
    {
        $column= ['uuid','trans_id','room_id','check_in','check_out','is_deleted','created_at','updated_at','created_by','updated_by'];
        $data = $this->service->getList($req, $column);
        return $this->getPaging($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trans_id'=>'required',
            'room_id'=>'required',
            'check_in'=>'required|date',
            'check_out'=>'required|date',
        ]);


       $params = $request->all();
       $room_using = $this->service->create($params);
       return $this->responseSuccess($room_using,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $roomUsing = $this->service->findFirstByUuid($request->uuid);
        if(!$roomUsing) return $this->response404();
        return $this->oneResponse($roomUsing);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'trans_id'=>'required',
            'room_id'=>'required',
            'check_in'=>'required|date',
            'check_out'=>'required|date',
        ]);
        $roomUsing = $this->service->findFirstByUuid($request->uuid);
        if(!$roomUsing) return $this->response404();
        $data = $this->service->update($roomUsing->id, $validated);
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $roomUsing = $this->service->findFirstByUuid($request->uuid);
        if(!$roomUsing) return $this->response404();
        $data = $this->service->delete($roomUsing->id);
        return $this->responseSuccess($roomUsing->uuid);
    }

    public function updateRoomUsingPayment(Request $request, $uuid)
    {
        $roomUsing = RoomUsing::where('uuid', $uuid)->first();

        if ($roomUsing) {
            // Cập nhật thông tin thanh toán
            $roomUsing->room_change_fee = $request->input('room_change_fee', $roomUsing->room_change_fee);
            $roomUsing->total_amount = $request->input('total_amount', $roomUsing->total_amount);
            $roomUsing->prepaid = $request->input('prepaid', $roomUsing->prepaid);
            $roomUsing->updated_at = now();
            $roomUsing->save();

            return response()->json(['message' => 'Payment details updated successfully.'], 200);
        }

        return response()->json(['message' => 'Room using record not found.'], 404);
    }


}
