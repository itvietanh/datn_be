<?php

namespace App\Services\Api;

use App\Models\EmployeeRole;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeRoleService extends BaseService
{
    public function __construct()
    {
        $this->model = new EmployeeRole();
    }

    // Lấy danh sách EmployeeRole với phân trang
    public function getList(Request $request, $columns = ['*'], $whereParams = null, $cacheable = false, $callback = null)
    {
        $query = $this->model->query()->select($columns);

        // Áp dụng callback nếu có
        if ($callback) {
            $callback($query);
        }

        // Áp dụng điều kiện where nếu có
        if ($whereParams) {
            $query->where($whereParams);
        }

        // Xử lý cache nếu có
        if ($cacheable) {
            return $query->cache()->paginate($request->get('per_page', 15));
        }

        return $query->paginate($request->get('per_page', 15));
    }


    // Tạo mới EmployeeRole
    public function create($data)
    {
        // Validate dữ liệu
        $validator = Validator::make($data, [
            'employee_id' => 'required|integer',
            'role_id' => 'required|integer',
            'created_by' => 'required|string|max:255',  // Kiểm tra trường created_by
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];  // Nếu có lỗi thì trả về thông báo lỗi
        }

        // Tạo mới EmployeeRole
        return $this->model->create($data);  // Nếu không có lỗi, tiến hành tạo mới
    }

    // Lấy EmployeeRole theo UUID
    public function findByUuid($uuid)
    {
        $employeeRole = $this->model->where('uuid', $uuid)->first();

        if (!$employeeRole) {
            throw new ModelNotFoundException("EmployeeRole not found.");
        }

        return $employeeRole;
    }

    // Cập nhật EmployeeRole theo UUID
    // public function update($uuid, $data, )
    // {
    //     $employeeRole = $this->findByUuid($uuid);

    //     // Validate dữ liệu
    //     $validator = Validator::make($data, [
    //         'employee_id' => 'required|integer',
    //         'role_id' => 'required|integer',
    //         'updated_by' => 'required|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return ['error' => $validator->errors()];
    //     }

    //     $employeeRole->update($data);

    //     return $employeeRole;
    // }

    // // Xóa EmployeeRole theo UUID
    // public function delete($uuid)
    // {
    //     $employeeRole = $this->findByUuid($uuid);

    //     $employeeRole->delete();

    //     return ['message' => 'EmployeeRole deleted successfully.'];
    // }
    public function update($id, $data)
    {
        $employeeRole = EmployeeRole::find($id);
        if (!$employeeRole) {
            return null;
        }

        $employeeRole->update($data);
        return $employeeRole;
    }

    public function delete($id)
    {
        $employeeRole = EmployeeRole::find($id);
        if (!$employeeRole) {
            return null;
        }

        $employeeRole->delete();
        return $employeeRole;
    }
}
