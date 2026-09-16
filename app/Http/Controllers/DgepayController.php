<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DgepayController extends Controller
{
    public function pay(Request $request)
    {
        $tranId = 'TRX_' . time() . rand(100, 999);
        $amount = $request->input('amount', 100);

        $payload = [
            'merchant_name'  => config('services.dgepay.merchant_name'),
            'tran_id'        => $tranId,
            'amount'         => (float) $amount,
            'currency'       => 'BDT',
            'redirect_url'   => config('services.dgepay.callback_url'),
            'customer_name'  => $request->input('name', 'John Doe'),
            'customer_email' => $request->input('email', 'customer@example.com'),
            'customer_phone' => $request->input('phone', '01618121655'),
        ];

        try {
            $response = Http::withHeaders([
                'Client-Id'       => config('services.dgepay.client_id'),
                'Client-Secret'   => config('services.dgepay.client_secret'),
                'Client-API-Key'  => config('services.dgepay.api_key'),
                'Accept'          => 'application/json',
                'Content-Type'    => 'application/json',
            ])->post(config('services.dgepay.base_url') . '/payment/create', $payload); // আপনার API endpoint অনুযায়ী path পরিবর্তন হতে পারে

            $result = $response->json();

            if ($response->successful() && isset($result['payment_url'])) {
                return redirect()->away($result['payment_url']);
            }

            return back()->with('error', $result['message'] ?? 'Payment initiation failed.');
        } catch (\Exception $e) {
            Log::error('DGePay Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong with the gateway.');
        }
    }
}
