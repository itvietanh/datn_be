<?php


namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use App\Models\PaymentMomo;

class MomoService
{
    protected $endpoint;
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $returnUrl;
    protected $notifyUrl;

    public function __construct()
    {
        // Cấu hình từ .env
        $this->endpoint = env('MOMO_ENDPOINT');
        $this->partnerCode = env('MOMO_PARTNER_CODE');
        $this->accessKey = env('MOMO_ACCESS_KEY');
        $this->secretKey = env('MOMO_SECRET_KEY');
        $this->returnUrl = env('MOMO_RETURN_URL');
        $this->notifyUrl = env('MOMO_NOTIFY_URL');
    }

    // Phương thức tạo giao dịch thanh toán
    public function createPayment($orderId, $amount, $orderInfo)
    {
        $requestId = time() . "";
        $requestData = [
            'partnerCode' => $this->partnerCode,
            'accessKey' => $this->accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $this->returnUrl,
            'notifyUrl' => $this->notifyUrl,
            'ipnUrl' => env('MOMO_NOTIFY_URL'), // Truyền ipnUrl
            'extraData' => '', // Trường extraData
            'requestType' => 'captureWallet',
        ];


        // Tạo chữ ký (signature)
         $requestData['extraData'] = '';

        $rawSignature = "partnerCode=MOMO&accessKey=F8BBA842ECF85&requestId=MM1540456472575&amount=150000&orderId=MM1540456472575&orderInfo=SDK team.&returnUrl=https://momo.vn&notifyUrl=https://momo.vn&extraData=email=abc@gmail.com
";


        $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

        // dd($rawSignature);
        $requestData['signature'] = $signature;


        // Gửi request tới MoMo API
        $response = Http::post($this->endpoint, $requestData);

        // Đăng chi tiết phản hồi để kiểm tra
        // \Log::info('Response from MoMo:', $response->json());

        return $response->json();
    }



    // // Phương thức kiểm tra trạng thái giao dịch (sử dụng MoMo Query API)
    // public function queryPaymentStatus($orderId)
    // {
    //     // Dữ liệu yêu cầu query
    //     $requestData = [
    //         'partnerCode' => $this->partnerCode,
    //         'accessKey' => $this->accessKey,
    //         'orderId' => $orderId,
    //     ];

    //     // Tạo chữ ký
    //     $rawSignature = "accessKey={$this->accessKey}&orderId={$orderId}&partnerCode={$this->partnerCode}";
    //     $signature = hash_hmac('sha256', $rawSignature, $this->secretKey);

    //     // Thêm chữ ký vào yêu cầu
    //     $requestData['signature'] = $signature;

    //     // Gửi yêu cầu query tới MoMo
    //     $response = Http::post($this->endpoint, $requestData);
    //     return $response->json();
    // }
}
