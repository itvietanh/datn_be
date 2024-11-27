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
        $employee = $this->service->findFirstByUuid($req->uuid);
        if(!$employee) $this -> response404();
        return $this -> oneResponse($employee);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $datareq = $req->all();
        $employee = $this->service->findFIrstByUuid($req->uuid);
        if(!$employee) $this-> response404();
        $data = $this->service->update($employee->id, $datareq);
        return $this-> responseSuccess($data);

    }


    public function destroy(Request $req)
    {
        $employee = $this->service->findFirstByUuid($req->uuid );
        if (!$employee) {
            return $this->response404();
        }
        $this->service->delete($employee->id);
        return $this->responseSuccess($employee);
    }
}
