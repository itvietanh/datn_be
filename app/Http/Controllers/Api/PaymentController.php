<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\PaymentMomo;
use Illuminate\Http\Request;
use App\Services\Api\MomoService;
use App\Models\Transaction;
use Illuminate\Support\Str;

class PaymentController extends BaseController
{

    protected $momoService;

    public function __construct(MomoService $momoService)
    {
        $this->momoService = $momoService;
    }

    public function createPayment(Request $request)
    {
        // Lấy dữ liệu từ request
        $amount = $request->input('amount');
        if (!$amount || $amount <= 0) {
            return $this->responseError('Số tiền thanh toán không hợp lệ.', 400);
        }

        // Tạo mã đơn hàng duy nhất
        $orderId = 'order_' . Str::random(10);
        $orderInfo = "Thanh toán hóa đơn #" . $orderId;

        // Gọi MoMo API để tạo yêu cầu thanh toán
        $response = $this->momoService->createPayment($orderId, $amount, $orderInfo);

        dd($response);

        if (isset($response['payUrl'])) {
            // Lưu thông tin giao dịch vào cơ sở dữ liệu
            PaymentMomo::create([
                'partner_code' => env('MOMO_PARTNER_CODE'),
                'order_id' => $orderId,   // Lưu order_id vào DB
                'amount' => $amount,
                'order_info' => $orderInfo,
                'order_type' => 'captureWallet',
                'trans_id' => null,  // Chưa có mã giao dịch lúc tạo
                'pay_type' => 'momo'
            ]);

            // Trả về URL thanh toán từ MoMo API
            $data = [
                'payment_url' => $response['payUrl'],
            ];

            return $this->responseSuccess($data, 'Link thanh toán được tạo thành công.');
        }

        return $this->responseError('Không thể tạo thanh toán, vui lòng thử lại.', 400);
    }

    public function handleReturn(Request $request)
{
    // Lấy thông tin giao dịch từ bảng payment_momo
    $transaction = PaymentMomo::where('order_id', $request->orderId)->first();

    if ($transaction) {
        // Cập nhật trạng thái giao dịch dựa trên kết quả trả về từ MoMo
        $status = $request->errorCode == 0 ? 'success' : 'failed';
        $transaction->update([
            'trans_id' => $request->transId,
            'pay_type' => $request->payType ?? 'Unknown',
            'status' => $status
        ]);

        return response()->json([
            'order_id' => $request->orderId,
            'status' => $status
        ]);
    }

    return response()->json([
        'message' => 'Giao dịch không tìm thấy.'
    ], 404);
}


}
