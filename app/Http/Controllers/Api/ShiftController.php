<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Shift;
use Illuminate\Http\Request;
use App\Services\Api\ShiftService;

class ShiftController extends BaseController
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    /**
     * Hiển thị danh sách ca làm việc
     */
    public function index(Request $request)
{
    $columns = ['id', 'name', 'description', 'salary'];

    $searchParams = (object) $request->only(['name', 'description']);

    $data = $this->shiftService->getList($request, $columns, function ($query) use ($searchParams) {
        if (isset($searchParams->name)) {
            $query->where('name', 'LIKE', '%' . $searchParams->name . '%');
        }
        if (isset($searchParams->description)) {
            $query->where('description', 'LIKE', '%' . $searchParams->description . '%');
        }
    });

    return $this->getPaging($data);
}


    /**
     * Lấy danh sách ca làm việc dạng combobox
     */
    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', 'name as label', 'description as desc', 'salary'];

        $searchParams = (object) $req->only(['id', 'q']);

        $data = $this->shiftService->getList($req, $fillable, function ($query) use ($searchParams) {
            if (!empty($searchParams->q)) {
                $query->where('name', 'like', '%' . $searchParams->q . '%');
            }

            if (!empty($searchParams->id)) {
                $query->where('id', '=', $searchParams->id);
            }
            $query->orderBy('id', 'asc');
        });
        return $this->getPaging($data);
    }

    /**
     * Tạo mới ca làm việc
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'salary' => 'required|numeric',
        ]);

        // Tạo mới ca làm việc
        $shift = $this->shiftService->create($data);

        return response()->json($shift, 201);
    }

    /**
     * Hiển thị ca làm việc theo UUID
     */
    public function show(Request $req)
    {
        // Tìm ca làm việc theo UUID
        $shift = Shift::where('id', $req->id)->first();

        if (!$shift) {
            return response()->json(['message' => 'Ca làm việc không tồn tại'], 404);
        }

        return $this->oneResponse($shift);
    }

    /**
     * Cập nhật ca làm việc theo UUID
     */
    public function update(Request $req)
    {
        // Xác thực dữ liệu
        $data = $req->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'salary' => 'required|numeric',
        ]);
        $shift = Shift::where('id', $req->id)->first();

        if (!$shift) {
            return response()->json(['message' => 'Ca làm việc không tồn tại'], 404);
        }
        $shift->update($data);

        return response()->json($shift);
    }
    public function destroy(Request $request)
    {
        $shift = $this->shiftService->findFirstById($request->id); // Thay $this->service thành $this->shiftService
        if (!$shift) {
            return $this->response404(); // Đảm bảo hàm này được định nghĩa trong BaseController
        }
        $this->shiftService->delete($shift->id); // Thay $this->service thành $this->shiftService
        return $this->responseSuccess($shift); // Đảm bảo hàm này được định nghĩa trong BaseController
    }
}
