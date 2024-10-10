<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class BaseService
{
    protected $model;

    public function create($params)
    {
        return $this->model->create($params);
    }

    public function update($id, $params)
    {
        $model = $this->model->find($id);
        $model->update($params);
        return $model;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item ? $item->delete() : true;
    }

    /**
     * Hàm xử lý phân trang cho danh sách.
     * 
     * @param Model $model Model được truyền vào
     * @param Request $request Yêu cầu HTTP
     * @param array $columns Danh sách các cột cần select
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request, $columns = ['*'], $whereParams = null)
    {
        $page = $request->query('page', 1);
        $size = $request->query('size', 20);
        $size = $size > 200 ? 200 : $size;
        $countable = filter_var($request->query('countable', true), FILTER_VALIDATE_BOOLEAN);

        $query = $this->model->select($columns);

        if ($whereParams && is_callable($whereParams)) {
            $whereParams($query);
        }
        if ($countable) {
            $data = $query->paginate($size, ['*'], 'page', $page);
        } else {
            $data = $query->simplePaginate($size, ['*'], 'page', $page);
        }
        return $data;
    }

    public function getListByWith(Request $request, array $columns = ['*'], callable $whereParams = null, array $with = [])
    {
        // Lấy thông tin phân trang từ request(Nếu null thì sử dụng giá trị mặc định)
        $page = (int) $request->query('page', 1);
        $size = (int) $request->query('size', 20);
        // Khởi tạo truy vấn với các cột được chỉ định và với mối quan hệ
        $query = $this->model->with($with)->select($columns);
        // Thực hiện các điều kiện (join, where)
        if ($whereParams && is_callable($whereParams)) {
            $whereParams($query);
        }
        $data = $query->paginate($size, ['*'], 'page', $page);
        return $data;
    }


    public function find($id, $with = null)
    {
        $query = $this->model;
        if ($with) {
            $query = $query->with($with);
        }
        return $query->find($id);
    }

    public function findFirstByUuid($uuid, $with = null)
    {
        $query = $this->model;
        if ($with) {
            $query = $query->with($with);
        }
        return $query->where('uuid', $uuid)->first();
    }

    public function deleteMore($ids)
    {
        return $this->model->destroy($ids);
    }

    public function deleteList($params)
    {
        try {
            DB::beginTransaction();

            foreach ($params['ids'] as $id) {
                $this->delete($id);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::debug($e);

            return false;
        }
    }

    protected function uploadFile($param, $field, $folder)
    {
        list($extension, $content) = explode(';', $param[$field]);
        $tmpExtension = explode('/', $extension);
        $fileName = Carbon::now()->timestamp . '.' . $tmpExtension[1];
        $content = explode(',', $content)[1];
        $test = Storage::put('public/' . $folder . '/' . $fileName, base64_decode($content));

        return $fileName;
    }
}
