<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\RoomUsingService;

class RoomUsingController extends Controller
{
    protected $service;
    public function _construct(RoomUsingService $service){
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $fillable=['uuid','trans_id','room_id','check_in','check_out','is_deleted','created_at','updated_at','created_by','updated_by'];
        $data = RoomUsing::select($fillable)->paginate($req->input('size:10'));
        return $this->getPaging($data);

    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $req->validate([
            'trans_id'=>'required|exists:transition,trans_id',
            'room_id'=>'required|string',
            'check_in'=>'required|date:Y-m-d H:i:s',
            'check_out'=>'required|date_format:Y-m-d H:i:s',
            'is_deleted'=>'required|boolean',
        ]);



       $params = $req->all();
       $room_using = RoomUsing::create([
        'uuid'=> \Illumante\Support\Str::uuid(),
        'trans_id'=>$validated['trans_id'],
        'room_id'=>$validated['room_id'],
        'check_in'=>$validated['check_in'],
        'check_out'=>$validated['check_out'],
        'is_deleted'=>$validated['is_deleted'],
       ]);
       return $this->responseSuccess($room_using,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room_using = RoomUsing::where('uuid',$uuid)->firstOrFail();
        return $this-> oneResponse($room_using);
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
    public function update(Request $request, string $id)
    {
        $validated = $req->validate([
            'trans_id'=>'required|exists:transition,trans_id',
            'room_id'=>'required|string',
            'check_in'=>'required|date',
            'check_out'=>'required|date',
            'is_deleted'=>'required|boolean',
        ]);
        $room_using = RoomUsing::where('uuid',uuid)->firstOrFail();
        $room_using->update([
        'trans_id'=>$validated['trans_id'],
        'room_id'=>$validated['room_id'],
        'check_in'=>$validated['check_in'],
        'check_out'=>$validated['check_out'],
        'is_deleted'=>$validated['is_deleted'],
        'updated_at'=>now(),
        
        ]);
        return $this->responseSuccess($room_using);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room_using = RoomUsing::where('uuid',uuid)->firstOrFail();
        $room_using->delete();
        return $this->responseSuccess([
            'message'=>'RoomUsing deleted successfully'
        ]);
    }
}
