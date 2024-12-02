<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Guest;
use App\Models\Room;
use App\Services\Api\OrderRoomService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;

use App\Services\Api\ImportDataService;

class OrderRoomController extends BaseController
{

    protected $service;

    public function __construct(OrderRoomService $service)
    {
        $this->service = $service;
    }

    public function store(Request $req)
    {
        // dd($req);
        $data = $this->service->handleOrderRoom($req);
        return $this->responseSuccess($data, 201);
    }

    public function calulatorPrice(Request $req)
    {
        $data = $this->service->handleCalculatorPrice($req);
        return $this->responseSuccess($data);
    }

    public function handleOverTime(Request $req)
    {
        $data = $this->service->updateStatusRoomOverTime($req->uuid);
        return $this->oneResponse($data->uuid);
    }

    public function handleRoomChange(Request $req)
    {
        $data = $this->service->changeRoom($req);
        return $this->responseSuccess($data);
    }

    public function handleSearchRooms(Request $req)
    {
        $data = $this->service->searchRoomsAvailable($req);
        return $data ? $this->getPaging($data) : $this->oneResponse($data);
    }

    public function importDataGuest(Request $req)
    {
        $req->validate([
            'file' => 'required|file|mimes:xlsx'
        ]);

        try {
            Excel::import(new ImportDataService, $req->file('file'));
            return $this->responseSuccess([
                "message" => "Import dữ liệu thành công"
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi import dữ liệu', 'error' => $e->getMessage()], 500);
        }
    }

    public function handleGetListRu(Request $req)
    {
        $data = DB::table('room_using as ru')->where('uuid', $req->ruUuid)->first();

        if ($data) {
            $ruId = $data->id;
        } else {
            return $this->responseError();
        }
        $query = DB::table('room_using_service as rus')
            ->select(
                's.service_name as serviceName',
                's.price',
                'sc.name as catName'
            )
            ->join('service as s', 's.id', '=', 'rus.service_id')
            ->join('service_categories as sc', 'sc.id', '=', 's.service_categories_id');

        if ($ruId) {
            $query->where('rus.room_using_id', $ruId);
        }
        $response = $this->service->getListQueryBuilder($req, $query);
        return $this->getPaging($response);
    }
}
