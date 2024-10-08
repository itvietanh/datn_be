<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

// Service
use App\Services\Api\EmployeeService;

// Request
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends BaseController
{

    protected $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $column = ['uuid', 'name', 'email', 'phone', 'address', 'hotel_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $data = $this->service->getList($req, $column);

        return $this->getPaging($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $req)
    {
        //
        $paramsData = $req->all();
        $data = $this->service->create($paramsData);
        return $this->responseSuccess($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $req)
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
