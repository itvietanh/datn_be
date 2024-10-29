<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Menu;

// Service
use App\Services\Api\MenuService;



class MenuController extends BaseController
{

    protected $service;

    public function __construct(MenuService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $columns = ['uuid', 'code', 'description', 'icon', 'idx', 'is_show', 'name', 'parent_uid', 'hotel_id'];

        $searchParams = (object) $request->only(['code', 'description', 'icon', 'idx', 'is_show', 'name', 'parent_uid', 'hotel_id']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {

            if (isset($searchParams->code)) {
                $query->where('code', 'LIKE', '%' . $searchParams->code . '%');
            }
            if (isset($searchParams->description)) {
                $query->where('description', 'LIKE', '%' . $searchParams->description . '%');
            }
            if (isset($searchParams->icon)) {
                $query->where('icon', 'LIKE', '%' . $searchParams->icon . '%');
            }
            if (isset($searchParams->idx)) {
                $query->where('idx', 'LIKE', '%' . $searchParams->idx . '%');
            }
            if (isset($searchParams->is_show)) {
                $query->where('is_show', 'LIKE', '%' . $searchParams->is_show . '%');
            }
            if (isset($searchParams->name)) {
                $query->where('name', 'LIKE', '%' . $searchParams->name . '%');
            }
            if (isset($searchParams->parent_uid)) {
                $query->where('parent_uid', 'LIKE', '%' . $searchParams->parent_uid . '%');
            }
            if (isset($searchParams->hotel_id)) {
                $query->where('hotel_id', 'LIKE', '%' . $searchParams->hotel_id . '%');
            }

        });
        return $this->getPaging($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $dataRe = $request->validate([
            'uuid' => 'required|uuid',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'idx' => 'required|integer',
            'is_show' => 'required|boolean',
            'name' => 'required|string|max:255',
            'parent_uid' => 'nullable|uuid',
            'hotel_id' => 'required|integer|exists:hotel,id',
        ]);

        // Gọi service để tạo Role mới
        $menu = $this->service->create($dataRe);

        // Trả về response thành công
        return $this->responseSuccess($menu, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $menu = $this->service->findFirstByUuid($req->uuid);
        if (!$menu)
            $this->response404();
        return $this->oneResponse($menu);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $dataRe = $req->validate([
            'uuid' => 'required|uuid',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'idx' => 'required|integer',
            'is_show' => 'required|boolean',
            'name' => 'required|string|max:255',
            'parent_uid' => 'nullable|uuid',
            'hotel_id' => 'required|integer|exists:hotel,id',
        ]);
        $menu = $this->service->findFirstByUuid($req->uuid);
        if (!$menu)
            $this->response404();
        $data = $this->service->update($menu->id, $dataRe);
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $menu = $this->service->findFirstByUuid($req->uuid);
        if (!$menu) $this->response404();
        $this->service->delete($menu->id);
        return $this->responseSuccess($menu);
    }
}
