<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\RoomUsingGuest;
use Illuminate\Http\Request;

class RoomUsingGuestController extends BaseController
{
    protected $service;

    public function __construct(RoomUsingGuest $service) {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guests = $this->service::all();
        return $this->responseSuccess($guests, 'Guests retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'uuid' => 'required|string|unique:room_using_guests',
            'guest_id' => 'required|integer',
            'room_using_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        $guest = $this->service::create($validatedData);
        return $this->responseSuccess($guest, 'Guest created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $guest = $this->service::find($uuid);
        if (!$guest) {
            return $this->response404('Guest not found.');
        }
        return $this->responseSuccess($guest, 'Guest retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $guest = $this->service::find($uuid);
        if (!$guest) {
            return $this->response404('Guest not found.');
        }

        $validatedData = $request->validate([
            'uuid' => 'sometimes|required|string|unique:room_using_guests,uuid,' . $uuid,
            'guest_id' => 'sometimes|required|integer',
            'room_using_id' => 'sometimes|required|integer',
            'check_in' => 'sometimes|required|date',
            'check_out' => 'sometimes|required|date',
        ]);

        $guest->update($validatedData);
        return $this->responseSuccess($guest, 'Guest updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $guest = $this->service::find($uuid);
        if (!$guest) {
            return $this->response404('Guest not found.');
        }

        $guest->delete();
        return $this->responseSuccess([], 'Guest deleted successfully.');
    }
}

