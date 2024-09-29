<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

// Request
use App\Http\Requests\TransitionRequest;
// Service
use App\Services\Api\TransitionService;


class TransitionController extends BaseController
{
    protected $service;

    public function __construct(TransitionService $service)
    {
        $this->service = $service;
    }


    /**
     * Lấy danh sách tất cả các transitions
     */
    public function index(Request $req)
    {
        $fillable = ['uuid', 'guest_id', 'transition_date', 'payment_status', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $data = $this->service->getList($req, $fillable);

        return $this->getPaging($data);
    }

    /**
     * Tạo một transition mới
     */
    public function store(Request $req)
    {
        $params = $req->all();
        $transition = $this->service->create($params);
        return $this->responseSuccess($transition, 201);
    }

    /**
     * Hiển thị một transition
     */
    public function show(Request $req)
    {
        $data = $this->service->findFirstByUuid($req->uuid);
        return $this->oneResponse($data);
    }


    /**
     * Cập nhật một transition
     */
    public function update(TransitionRequest $req)
    {
        $transition = $this->service->findFirstByUuid($req->uuid);
        if (!$transition) {
            return $this->response404();
        }
        $data = $this->service->update($transition->id, $req->all());
        return $this->responseSuccess($data);
    }

    /**
     * Xóa một transition
     */
    public function destroy(Request $req)
    {
        $transition = $this->service->findFirstByUuid($req->uuid);
        if (!$transition) {
            return $this->response404();
        }
        $data = $this->service->delete($transition->id);
        return $this->responseSuccess($data);
    }
}
