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

    public function payWithMoMo(Request $request)
    {
        $orderId = time(); // Mã đơn hàng
        $amount = $request->amount; // Tổng tiền
        $orderInfo = "Thanh toán đơn hàng";
        $redirectUrl = route('payment.callback'); // URL trả về khi thanh toán thành công
        $ipnUrl = route('payment.ipn'); // URL MoMo gọi khi thanh toán thành công

        $response = MoMoService::createPayment($orderId, $amount, $orderInfo, $redirectUrl, $ipnUrl);

        if (isset($response['payUrl'])) {
            return $this->oneResponse(['payUrl' => $response['payUrl']]);
        }

        return response()->json(['error' => 'Payment failed!'], 500);
    }

    // public function callback(Request $request)
    // {
    //     return view('payment.callback', ['data' => $request->all()]);
    // }
    // public function ipn(Request $request)
    // {
    //     return response()->json(['message' => 'IPN received']);
    // }
    public function callback(Request $request)
{
    // Lấy dữ liệu trả về từ MoMo
    $data = $request->all();

    // Kiểm tra kết quả giao dịch
    if (isset($data['resultCode']) && $data['resultCode'] == 0) {
        // Giao dịch thành công
        // Cập nhật trạng thái đơn hàng trong database (ví dụ)
        $orderId = $data['orderId'];
        // Ví dụ: $this->updateOrderStatus($orderId, 'paid');

        return view('payment.success', [
            'message' => 'Thanh toán thành công!',
            'data' => $data,
        ]);
    } else {
        // Giao dịch thất bại hoặc bị hủy
        return view('payment.fail', [
            'message' => 'Thanh toán thất bại!',
            'data' => $data,
        ]);
    }
}
public function ipn(Request $request)
{
    $data = $request->all();

    // Kiểm tra kết quả giao dịch
    if (isset($data['resultCode']) && $data['resultCode'] == 0) {
        // Giao dịch thành công
        $orderId = $data['orderId'];
        $amount = $data['amount'];

        // Cập nhật trạng thái đơn hàng (ví dụ)
        // $this->updateOrderStatus($orderId, 'paid');

        return response()->json(['message' => 'IPN processed successfully'], 200);
    } else {
        // Giao dịch thất bại hoặc có lỗi
        return response()->json(['message' => 'IPN failed'], 400);
    }
}



}
