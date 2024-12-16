<?php


namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use App\Models\PaymentMomo;
use GuzzleHttp\Client;

class MomoService
{
    public static function createPayment($orderId, $amount, $orderInfo, $redirectUrl, $ipnUrl)
    {
        $endpoint = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');

        $requestId = time() . "";
        $requestType = "payWithATM";
        $extraData = ""; // Custom data

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $client = new Client();
        $response = $client->post($endpoint, [
            'json' => [
                'partnerCode' => $partnerCode,
                'accessKey' => $accessKey,
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature,
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
