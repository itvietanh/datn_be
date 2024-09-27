<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Guest;
use Illuminate\Http\Request;

// Service
use App\Services\Api\GuestService;

class EmployeeController extends BaseController
{

    protected $service;

    public function __construct(GuestService $service)
    {
        $this->service = $service;   
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Guest::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $guest = Guest::create($request->validated());
        return response()->json($guest, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Guest::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guest = Guest::findOrFail($id);
        $guest->update($request->validated());
        return response()->json($guest);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guest = Guest::findOrFail($id);
        $guest->delete();
        return response()->json(null, 204);
    }
}
