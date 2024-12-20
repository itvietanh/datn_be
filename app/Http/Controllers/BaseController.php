<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class BaseController extends Controller
{
    //
    public function __construct() {}

    /**
     * Hàm xử lý phân trang cho danh sách.
     *
     * @param Model $model Model được truyền vào
     * @param Request $request Yêu cầu HTTP
     * @param array $columns Danh sách các cột cần select
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaging($data)
    {
        $meta = [
            'page' => $data->currentPage(),
            'size' => $data->perPage(),
            'total' => method_exists($data, 'total') ? $data->total() : null,
            'hasNextPage' => $data->hasMorePages(),
        ];

        return response()->json([
            'code' => 'OK',
            'data' => [
                'items' => $data->items(),
                'meta' => $meta
            ]
        ]);
    }


    /**
     * Hàm lấy một kết quả từ model.
     *
     * @param Model $model Model được truyền vào
     * @param int|string $id ID của item cần lấy
     * @param array $columns Danh sách các cột cần select
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneResponse($data)
    {
        if (!$data) {
            $this->response404();
        }

        return response()->json([
            'code' => 'OK',
            'data' => $data
        ]);
    }

    public function responseSuccess($data = [], $code = null)
    {
        return response()->json([
            'code' => 'OK',
            'message' => 'Success',
            'data' => $data
        ], $code ? $code : 200);
    }

    public function response404()
    {
        return response()->json([
            'code' => 'NOT_FOUND',
            'errors' => [
                'message' => "Không tìm thấy dữ liệu"
            ]
        ], 404);
    }

    public function responseError($message = "", $code = null)
    {
        return response()->json([
            'code' => 'NOT_FOUND',
            'errors' => [
                'message' => $message ? $message : "Đã có lỗi xảy ra, vui lòng thử lại!"
            ]
        ], $code ? $code : 500);
    }
}
