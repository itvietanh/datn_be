<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Api\EmployeeRoleService;

class EmployeeRoleController extends BaseController
{
    protected $service;
    public function __construct(EmployeeRoleService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $columns = ['employee_id', 'role_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        
        $searchParams = (object) $request->only(['employee_id', 'role_id']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            if (isset($searchParams->employee_id)) {
                $query->where('employee_id', '=', $searchParams->employee_id);
            }
            if (isset($searchParams->role_id)) {
                $query->where('role_id', '=', $searchParams->role_id);
            }
        });
        return $this->getPaging($data);
    }


    /**
     * Show the form for creating a new resource.
     */
    // public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataReq = $request->validate([
            'employee_id' => 'required|integer',
            'role_id' => 'required|integer'
        ]);

        $EmployeeRole = $this->service->create($dataReq);

        return $this->responseSuccess($EmployeeRole, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $EmployeeRole = $this->service->findFirstByUuid($req->uuid);
        if (!$EmployeeRole) $this->response404();
        return $this->oneResponse($EmployeeRole);
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
    public function update(Request $req)
    {
        $dataReq = $req->validate([
            'employee_id' => 'required|integer',
            'role_id' => 'required|integer'
        ]);
        $EmployeeRole = $this->service->findFirstByUuid($req->uuid);
        if (!$EmployeeRole) $this->response404();
        $data = $this->service->update($EmployeeRole->id, $dataReq);
        return $this->responseSuccess($data);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $EmployeeRole = $this->service->findFirstByUuid($req->uuid);
        if (!$EmployeeRole) $this->response404();
        $this->service->delete($EmployeeRole->id);
        return $this->responseSuccess($EmployeeRole);
    }
}
