<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Services\Api\PaymentMethodService;
use Illuminate\Support\Str;

class PaymentMethodController extends BaseController
{
    protected $paymentMethodService;

    public function __construct(PaymentMethodService $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
    }

    /**
     * Hiển thị danh sách phương thức thanh toán
     */
    public function index(Request $request)
    {
        $columns = ['uuid', 'name', 'pr_code', 'description', 'created_at', 'updated_at'];

        $searchParams = (object) $request->only(['name', 'description']);

        $data = $this->paymentMethodService->getList($request, $columns, function ($query) use ($searchParams) {
            if (isset($searchParams->name)) {
                $query->where('name', 'LIKE', '%' . $searchParams->name . '%');
            }
            if (isset($searchParams->description)) {
                $query->where('description', 'LIKE', '%' . $searchParams->description . '%');
            }
        });

        return response()->json($data);
    }

    public function getCombobox(Request $req)
    {
        $fillable = ['id as value', 'name as label', 'qr_code as qrCode', 'description as desc', 'icon'];

        $searchParams = (object) $req->only(['id', 'q']);

        $data = $this->paymentMethodService->getList($req, $fillable, function ($query) use ($searchParams) {
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
     * Tạo mới phương thức thanh toán
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'pr_code' => 'required|string|max:50',
            'description' => 'required|string'
        ]);

        // Tạo mới phương thức thanh toán
        $paymentMethod = $this->paymentMethodService->create($data);

        return response()->json($paymentMethod, 201);
    }

    /**
     * Hiển thị phương thức thanh toán theo UUID
     */
    public function show($uuid)
    {
        // Tìm phương thức thanh toán theo UUID
        $paymentMethod = PaymentMethod::where('uuid', $uuid)->first();

        if (!$paymentMethod) {
            return response()->json(['message' => 'Phương thức thanh toán không tồn tại'], 404);
        }

        return response()->json($paymentMethod);
    }

    /**
     * Cập nhật phương thức thanh toán theo UUID
     */
    public function update(Request $request, $uuid)
    {
        // Xác thực dữ liệu
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'pr_code' => 'required|string|max:50',
            'description' => 'required|string'
        ]);

        // Tìm phương thức thanh toán theo UUID (lấy một bản ghi duy nhất)
        $paymentMethod = PaymentMethod::where('uuid', $uuid)->first();

        if (!$paymentMethod) {
            return response()->json(['message' => 'Phương thức thanh toán không tồn tại'], 404);
        }

        // Cập nhật phương thức thanh toán
        $paymentMethod->update($data);

        return response()->json($paymentMethod);
    }

    /**
     * Xóa phương thức thanh toán theo UUID
     */
    public function destroy($uuid)
    {
        // Tìm phương thức thanh toán theo UUID
        $paymentMethod = PaymentMethod::where('uuid', $uuid)->first();

        if (!$paymentMethod) {
            return response()->json(['message' => 'Phương thức thanh toán không tồn tại'], 404);
        }

        // Xóa phương thức thanh toán
        $paymentMethod->delete();

        return response()->json(['message' => 'Phương thức thanh toán đã được xóa thành công']);
    }
}
