<?php


namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Services\NagadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NagadPaymentController extends Controller
{
    public function pay(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to purchase a package.');
        }

        $package = PricingPlan::where('id', $request->input('package_id'))->where('is_active', true)->first();

        if (!$package) {
            return redirect()->back()->withErrors(['package_id' => 'Invalid or inactive package selected.']);
        }

        $merchantId = config('services.nagad.merchant_id');
        $dateTime = now()->timezone('Asia/Dhaka')->format('YmdHis');
        $orderNumber = 'ORD' . strtoupper(Str::random(10));
        $challenge = Str::random(40);
        $amount = $package->min_order_amount > 0 ? $package->min_order_amount : 100;

        // Calculate SMS Count
        $pricePerSms = (float) preg_replace('/[^0-9.]/', '', $package->price);
        $smsCount = ($pricePerSms > 0) ? (int) floor($amount / $pricePerSms) : 0;

        // Create Pending Order & Payment in DB
        $order = Order::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'order_number' => $orderNumber,
            'package_name' => $package->name,
            'plan_type' => $package->plan_type,
            'price_per_sms' => $pricePerSms,
            'amount' => $amount,
            'sms_count' => $smsCount,
            'payment_method' => 'nagad',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'gateway' => 'nagad',
            'amount' => $amount,
            'charge' => 0.00,
            'currency' => 'BDT',
            'status' => 'pending',
        ]);

        Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'user_id' => $user->id,
            'order_id' => $order->id,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'plan_type' => $package->plan_type,
            'price_per_sms' => $pricePerSms,
            'sms_count' => $smsCount,
            'amount' => $amount,
            'paid_amount' => 0,
            'payment_method' => 'nagad',
            'status' => 'unpaid',
            'due_date' => now(),
            'created_by' => 'user',
        ]);

        // Notify Admins
        AppNotification::sendToAdmins(
            'New Order Placed: #' . $orderNumber,
            "{$user->name} placed an order for {$package->name} worth ৳" . number_format($amount, 2) . ' via Nagad.',
            'order',
            route('admin.invoices.index'),
            'fas fa-shopping-cart text-danger'
        );

        // Step 1: Initialize Payment API Call
        $sensitiveDataArray = [
            'merchantId' => $merchantId,
            'datetime' => $dateTime,
            'orderId' => $orderNumber,
            'challenge' => $challenge
        ];

        $postData = [
            'accountNumber' => config('services.nagad.account_number'),
            'dateTime' => $dateTime,
            'sensitiveData' => NagadService::encryptPublicKey(json_encode($sensitiveDataArray)),
            'signature' => NagadService::generateSignature(json_encode($sensitiveDataArray))
        ];

        $initUrl = config('services.nagad.base_url') . "/api/dfs/check-out/initialize/{$merchantId}/{$orderNumber}";

        $response = Http::withHeaders([
            'X-KM-Api-Version' => 'v-0.2.0',
            'X-KM-IP-V4' => NagadService::getClientIp(),
            'X-KM-Client-Type' => 'PC_WEB',
            'Content-Type' => 'application/json'
        ])->post($initUrl, $postData)->json();

        if (isset($response['sensitiveData'])) {
            $decryptedData = json_decode(NagadService::decryptPrivateKey($response['sensitiveData']), true);
            
            $paymentRefId = $decryptedData['paymentReferenceId'] ?? null;
            $challenge = $decryptedData['challenge'] ?? null;

            if ($paymentRefId && $challenge) {
                // Update payment ref id
                $payment->update([
                    'payment_ref_id' => $paymentRefId,
                ]);

                // Step 2: Complete Initialization
                $sensitivePayload = [
                    'merchantId' => $merchantId,
                    'orderId' => $orderNumber,
                    'currencyCode' => '050',
                    'amount' => $amount,
                    'challenge' => $challenge
                ];

                $completeData = [
                    'paymentRefId' => $paymentRefId,
                    'sensitiveData' => NagadService::encryptPublicKey(json_encode($sensitivePayload)),
                    'signature' => NagadService::generateSignature(json_encode($sensitivePayload)),
                    'merchantCallbackURL' => config('services.nagad.callback_url')
                ];

                $completeUrl = config('services.nagad.base_url') . "/api/dfs/check-out/complete/{$paymentRefId}";

                $finalResponse = Http::withHeaders([
                    'X-KM-Api-Version' => 'v-0.2.0',
                    'X-KM-IP-V4' => NagadService::getClientIp(),
                    'X-KM-Client-Type' => 'PC_WEB',
                    'Content-Type' => 'application/json'
                ])->post($completeUrl, $completeData)->json();

                if (isset($finalResponse['status']) && strtolower($finalResponse['status']) === 'success' && !empty($finalResponse['callBackUrl'])) {
                    return redirect()->away($finalResponse['callBackUrl']);
                }
            }
        }

        $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
        $payment->update(['status' => 'failed']);

        return redirect()->route('user.buy_package')->with('error', 'Nagad Payment Initialization Failed: ' . ($response['message'] ?? 'Please try again.'));
    }

    public function callback(Request $request)
    {
        $status = $request->input('status');
        $paymentRefId = $request->input('payment_ref_id');
        $orderNumber = $request->input('order_id');

        Log::info('Nagad Callback Received:', $request->all());

        // Find Payment record
        $payment = Payment::where('payment_ref_id', $paymentRefId)->first();
        if (!$payment && $orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order) {
                $payment = Payment::where('order_id', $order->id)->first();
            }
        }

        if (strtolower($status) === 'success' && $paymentRefId) {
            // Step 3: Verify Payment Status with Nagad
            $verifyUrl = config('services.nagad.base_url') . "/api/dfs/verify/payment/{$paymentRefId}";

            $verifyResponse = Http::withHeaders([
                'X-KM-Api-Version' => 'v-0.2.0',
                'X-KM-IP-V4' => NagadService::getClientIp(),
                'X-KM-Client-Type' => 'PC_WEB',
            ])->get($verifyUrl)->json();

            Log::info('Nagad Verify Response:', $verifyResponse ?? []);

            if (isset($verifyResponse['status']) && strtoupper($verifyResponse['status']) === 'SUCCESS') {
                DB::transaction(function () use ($payment, $verifyResponse, $paymentRefId) {
                    $trxId = $verifyResponse['issuerPaymentRefNo'] ?? ($verifyResponse['paymentRefId'] ?? $paymentRefId);

                    if ($payment) {
                        $payment->update([
                            'trx_id' => $trxId,
                            'status' => 'success',
                            'response_data' => $verifyResponse,
                            'paid_at' => now(),
                        ]);

                        if ($payment->order) {
                            $payment->order->update([
                                'payment_status' => 'completed',
                                'status' => 'completed',
                            ]);

                            if ($payment->order->invoice) {
                                $payment->order->invoice->update([
                                    'status' => 'paid',
                                    'paid_amount' => $payment->amount,
                                    'paid_at' => now(),
                                    'trx_id' => $trxId,
                                ]);
                            }

                            // Credit user SMS balance
                            if ($payment->user && $payment->order->sms_count > 0) {
                                $payment->user->increment('sms_balance', $payment->order->sms_count);
                            }
                        }
                    }
                });

                return redirect()->route('user.payment_history')->with('success', 'Payment of ৳' . ($verifyResponse['amount'] ?? '') . ' completed successfully! Your order has been placed.');
            }
        }

        // If payment cancelled or failed
        if ($payment) {
            $payment->update([
                'status' => (strtolower($status) === 'aborted' || strtolower($status) === 'cancelled') ? 'cancelled' : 'failed',
                'response_data' => $request->all(),
            ]);

            if ($payment->order) {
                $payment->order->update([
                    'payment_status' => (strtolower($status) === 'aborted' || strtolower($status) === 'cancelled') ? 'cancelled' : 'failed',
                    'status' => (strtolower($status) === 'aborted' || strtolower($status) === 'cancelled') ? 'cancelled' : 'failed',
                ]);
            }
        }

        return redirect()->route('user.orders')->with('error', 'Payment was ' . ($status ?: 'failed') . '. Please try again if you wish.');
    }
}