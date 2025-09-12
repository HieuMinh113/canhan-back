<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
use App\Services\BillService;

class PaymentController extends Controller
{
     protected $billService;

    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function momo_payment(Request $request)
    {   
        $amount = $request->input('amount', 10000);
        $billResponse = $this->billService->add($request);
        $billData = json_decode($billResponse->getContent(), true)['bill'];
        $bill = Bill::find($billData['id']);
        $orderId = $bill->id;
        
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey   = 'klm05TvNBzhg7h7j';
        $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        $orderInfo = "Thanh toán qua MoMo";
        $amount    = $request->input('amount', 10000); 
        $orderId   = time() . "";
        $redirectUrl = "http://localhost:8080/cart"; 
        $ipnUrl      = "/momo-callback"; 
        $extraData   = "";

        $requestId   = time() . "";
        $requestType = "payWithATM";

        $rawHash = "accessKey=" . $accessKey .
                   "&amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&ipnUrl=" . $ipnUrl .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&partnerCode=" . $partnerCode .
                   "&redirectUrl=" . $redirectUrl .
                   "&requestId=" . $requestId .
                   "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId"     => "MomoTestStore",
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        );

        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);
        return response()->json([
            'payUrl' => $jsonResult['payUrl'] ?? null,
            'orderId' => $orderId,
            'amount' => $amount
        ]);
    }
    public function momoCallback(Request $request)
{
    if ($request->resultCode == '0') {
        $bill = Bill::where('id', $request->orderId)->first();
        if ($bill) {
            $bill->update([
                'status' => 'paid',
                'payment_method' => 'momo',
                'payment_type' => $request->orderType ?? 'ATM',
                'transaction_id' => $request->transId,
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công',
            'bill' => $bill
        ]);
    }
    return response()->json([
        'success' => false,
        'message' => 'Thanh toán thất bại'
    ]);
}
}
