<?php
namespace App\Http\Controllers\Api;

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
        $columns = ['id', 'uuid', 'role_name', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by'];
        $searchParams = (object) $request->only(['role_name']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            if (!empty($searchParams->role_name)) {
                $query->where('role_name', 'LIKE', "%{$searchParams->role_name}%");
            }
        });

        return $this->getPaging($data);
    }

    public function store(Request $request)
    {
        $dataReq = $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $role = $this->service->create($dataReq);

        return $this->responseSuccess($role, 201);
    }

    public function show(Request $request)
    {
        $role = $this->service->findFirstByUuid($request->uuid);
        if (!$role) $this->response404();
        return $this->oneResponse($role);
    }

    public function update(Request $request)
    {
        $dataReq = $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $role = $this->service->findFirstByUuid($request->uuid);
        if (!$role) $this->response404();

        $updatedRole = $this->service->update($role->id, $dataReq);
        return $this->responseSuccess($updatedRole);
    }

    public function destroy(Request $request)
    {
        $role = $this->service->findFirstByUuid($request->uuid);
        if (!$role) $this->response404();

        $this->service->delete($role->id);
        return $this->responseSuccess($role);
    }
}
