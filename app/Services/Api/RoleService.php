<?php
namespace App\Services\Api;

use App\Models\Role;
use Illuminate\Support\Str;

class RoleService
{
    public function getList($request, $columns, $callback = null)
    {
        $query = Role::query()->select($columns);

        if ($callback) {
            $callback($query);
        }

        return $query->paginate($request->get('per_page', 15));
    }

    public function create($data)
    {
        $data['uuid'] = \Str::uuid();
        return Role::create($data);
    }

    public function findFirstByUuid($uuid)
    {
        return Role::where('uuid', $uuid)->first();
    }

    public function update($id, $data)
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return $role;
    }
}
