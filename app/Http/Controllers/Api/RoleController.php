<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Api\RoleService;

class RoleController extends BaseController
{
    protected $service;
    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $columns = ['uuid', 'role_name', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $searchParams = (object) $request->only(['role_name', 'description']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {

            if (isset($searchParams->role_name)) {
                $query->where('role_name', 'LIKE', '%' . $searchParams->role_name . '%');
            }
            if (isset($searchParams->description)) {
                $query->where('description', 'LIKE', '%' . $searchParams->description . '%');
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
        $dataRe = $request->validate([
            'role_name' => 'required|integer',
            'description' => 'required|integer',
        ]);

        $Role = $this->service->create($dataRe);

        return $this->responseSuccess($Role, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $role = $this->service->findFirstByUuid($req->uuid);
        if (!$role) $this->response404();
        return $this->oneResponse($role);
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
        $dataRe = $req->validate([
            'role_name' => 'required|integer',
            'description' => 'required|integer'
        ]);
        $role = $this->service->findFirstByUuid($req->uuid);
        if (!$role) $this->response404();
        $data = $this->service->update($role->id,$dataRe);
        return $this->responseSuccess($data);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $role = $this->service->findFirstByUuid($req->uuid);
        if (!$role) $this->response404();
        $this->service->delete($role->id);
        return $this->responseSuccess($role);
    }
}
