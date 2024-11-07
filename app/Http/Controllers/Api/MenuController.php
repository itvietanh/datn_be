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
        $columns = ['id', 'code', 'description', 'icon', 'idx', 'is_show', 'name', 'parent_uid', 'hotel_id'];

        $searchParams = (object) $request->only(['code', 'description', 'icon', 'idx', 'is_show', 'name', 'parent_uid', 'hotel_id']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {

            if (isset($searchParams->code)) {
                $query->where('code', 'LIKE', '%' . $searchParams->code . '%');
            }
            if (isset($searchParams->id)) {
                $query->where('id', 'LIKE', '%' . $searchParams->id . '%');
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

        // Gọi service để tạo Role mới
        $Menu = $this->service->create($request->all());

        // Trả về response thành công
        return $this->responseSuccess($Menu, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $req)
    {
        $Menu = $this->service->findFirstByUuid($req->id);
        if (!$Menu)
            $this->response404();
        return $this->oneResponse($Menu);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $req)
    {
        $Menu = $this->service->findFirstByUuid($req->id);
        if (!$Menu)
            $this->response404();
        $data = $this->service->update($Menu->id, $req->all());
        return $this->responseSuccess($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $req)
    {
        $Menu = $this->service->findFirstByUuid($req->id);
        if (!$Menu) $this->response404();
        $this->service->delete($Menu->id);
        return $this->responseSuccess($Menu);
    }
}
