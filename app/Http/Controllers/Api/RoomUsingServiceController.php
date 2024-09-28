<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Api\RoomUsingService;

class RoomUsingServiceController extends BaseController
{
    
    protected $service;

    public function __construct(RoomUsingService $service) {
        $this->service = $service;
    }

    public function index()
    {
        // return RoomUsingService::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_using_id' => 'required|integer',
            'service_id' => 'required|integer',
            'service_using_date' => 'required|date',
            'created_by' => 'nullable|integer',
        ]);
        //postman dau nhi

        $service = $this->service->create($data);
        return $this->responseSuccess($service, 201);

        //Đó mình làm như này. cái response kia tôi tự build để trả ra chuẩn response cho client
    }

    //đấy làm như này nhé, easy mà nó dễ xử lý nếu gặp trường hợp khó

    public function show(Request $req)
    {
        $roomUsingServ = $this->service->findFirstByUuid($req->uuid);
        return $this->oneResponse($roomUsingServ);
    }


    public function update(Request $request, $uuid)
{
    // Xác thực dữ liệu đầu vào
    $data = $request->validate([
        'room_using_id' => 'required|integer',
        'service_id' => 'required|integer',
        'service_using_date' => 'required|date',
        'updated_by' => 'nullable|integer',
    ]);

    // Tìm RoomUsingService theo UUID
    $roomUsingServ = $this->service->findFirstByUuid($uuid);

    // Kiểm tra nếu không tìm thấy
    if (!$roomUsingServ) {
        return $this->response404('Room Using Service not found', 404);
    }

    // Cập nhật dữ liệu
    $roomUsingServ->update($data);

    // Trả về dữ liệu sau khi cập nhật
    return $this->responseSuccess($roomUsingServ);
}


    public function destroy($uuid)
{
    // Tìm RoomUsingService theo UUID
    $roomUsingServ = $this->service->findFirstByUuid($uuid);

    // Kiểm tra nếu không tìm thấy
    if (!$roomUsingServ) {
        return $this->response404('Room Using Service not found', 404);
    }

    // Xóa bản ghi
    $roomUsingServ->delete();

    // Trả về phản hồi thành công
    return $this->responseSuccess(null, 204);
}


}
