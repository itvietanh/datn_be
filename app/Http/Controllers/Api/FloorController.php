<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Floor;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\Api\FloorService;

class FloorController extends BaseController
{
    protected $service;
    public function __construct(FloorService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $columns = ['uuid', 'hotel_id', 'floor_number', 'created_at', 'updated_at', 'created_by', 'updated_by'];

        $searchParams = (object) $request->only(['hotel_id', 'floor_number']);

        $data = $this->service->getList($request, $columns, function ($query) use ($searchParams) {
            if (isset($searchParams->hotel_id)) {
                $query->where('hotel_id', '=', $searchParams->hotel_id);
            }
            if (isset($searchParams->floor_number)) {
                $query->where('floor_number', '=', $searchParams->floor_number);
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
        $request->validate([
            'hotel_id' => 'required|integer',
            'floor_number' => 'required|integer'

        ]);

        $floor = Floor::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'hotel_id' => $request->hotel_id,
            'floor_number' => $request->floor_number
        ]);

        return response()->json(['message' => 'Bản ghi đã được thêm thành công!', 'data' => $floor], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $floor = Floor::find($id);
        if (!$floor) {
            return response()->json(['message' => 'Lỗi'], 404);
        }
        return response()->json($floor);
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
    public function update(Request $request, string $id)
    {
        $floor = Floor::find($id);
        if (!$floor) {
            return response()->json(['message' => 'Floor not found'], 404);
        }

        $request->validate([
            'floor_number' => 'sometimes|required|integer',
        ]);

        // Cập nhật các trường dữ liệu
        $floor->update($request->only('floor_number'));

        return response()->json(['message' => 'Cập nhật thành công!', 'data' => $floor], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $floor = Floor::where('uuid', $uuid)->firstOrFail();

        $floor->delete();

        return $this->responseSuccess([
            'message' => 'Transition deleted successfully'
        ]);
    }
}
