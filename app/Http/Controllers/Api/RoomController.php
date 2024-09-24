<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Room;

class RoomController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $fillAble = ['uuid', 'hotel_id', 'floor_id', 'room_type_id', 'room_number', 'status', 'max_capacity','created_at', 'updated_at', 'created_by', 'updated_by'];
        return $this->getPaging(Room::query(), $req, $fillAble);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
