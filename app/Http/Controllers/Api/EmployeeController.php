<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

// Service
use App\Services\Api\EmployeeService;

// Request
use App\Http\Requests\EmployeeRequest;
use App\Models\EmployeeRole;
use Illuminate\Support\Facades\DB;

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
        $column = ['e.uuid', 'e.name', 'e.email', 'e.phone', 'e.address', 'e.hotel_id', 'e.created_at', 'e.updated_at', 'e.created_by', 'e.updated_by', 'r.role_name', 'e.status'];

        $searchParams = (object) $req->all();

        $data = $this->service->getList($req, $column, function ($query) use ($searchParams) {
            $query->from('employee as e')
                ->leftJoin('employee_role as er', 'e.id', '=', 'er.employee_id')
                ->leftJoin('role as r', 'er.role_id', '=', 'r.id');

            if (!empty($searchParams->name)) {
                $query->where('name', 'like', '%' . $searchParams->name . '%');
            }

            if (!empty($searchParams->address)) {
                $query->where('address', 'like', '%' . $searchParams->address . '%');
            }
        });

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

        $employeeRole = new EmployeeRole();
        $employeeRole->employee_id = $data->id;
        $employeeRole->role_id = $paramsData['role_id'];
        $employeeRole->save();

        return $this->responseSuccess($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $data = DB::table('employee as e')
            ->select('e.uuid', 'e.name', 'e.email', 'e.phone', 'e.address', 'e.hotel_id', 'e.created_at', 'e.updated_at', 'e.created_by', 'e.updated_by', 'r.id as role_id', 'e.password', 'e.status')
            ->leftJoin('employee_role as er', 'e.id', '=', 'er.employee_id')
            ->leftJoin('role as r', 'er.role_id', '=', 'r.id')
            ->where('e.uuid', $req->uuid)->first();

        if (!$data) $this->response404();
        return $this->oneResponse($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $datareq = $req->all();
        $employee = $this->service->findFIrstByUuid($req->uuid);
        if (!$employee) $this->response404();
        $data = $this->service->update($employee->id, $datareq);

        $employeeRole = EmployeeRole::where('employee_id', $employee->id)->first();
        $employeeRole->role_id = $datareq['role_id'];
        $employeeRole->save();

        return $this->responseSuccess($data);
    }


    public function destroy(Request $req)
    {
        $employee = $this->service->findFirstByUuid($req->uuid);
        if (!$employee) {
            return $this->response404();
        }
        Shift::where('employee_id', $employee->id)->delete();
        $this->service->delete($employee->id);
        return $this->responseSuccess($employee);
    }
}
