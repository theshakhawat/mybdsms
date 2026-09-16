<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Services\DgepayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DgepayController extends Controller
{
    protected DgepayService $dgepayService;

    public function __construct(DgepayService $dgepayService)
    {
        $this->dgepayService = $dgepayService;
    }

    /**
     * Initiate payment when user selects DGePay on /user/buy-package.
     */
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

        $orderNumber = 'ORD' . strtoupper(Str::random(10));
        $amount      = $package->min_order_amount > 0 ? (float) $package->min_order_amount : 100.0;

        // Calculate SMS count
        $pricePerSms = (float) preg_replace('/[^0-9.]/', '', $package->price);
        $smsCount    = ($pricePerSms > 0) ? (int) floor($amount / $pricePerSms) : 0;

        // Create Pending Order & Payment in DB
        $order = Order::create([
            'user_id'        => $user->id,
            'package_id'     => $package->id,
            'order_number'   => $orderNumber,
            'package_name'   => $package->name,
            'plan_type'      => $package->plan_type,
            'price_per_sms'  => $pricePerSms,
            'amount'         => $amount,
            'sms_count'      => $smsCount,
            'payment_method' => 'dgepay',
            'payment_status' => 'pending',
            'status'         => 'pending',
        ]);

        $payment = Payment::create([
            'user_id'  => $user->id,
            'order_id' => $order->id,
            'gateway'  => 'dgepay',
            'amount'   => $amount,
            'charge'   => 0.00,
            'currency' => 'BDT',
            'status'   => 'pending',
        ]);

        Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'user_id'        => $user->id,
            'order_id'       => $order->id,
            'package_id'     => $package->id,
            'package_name'   => $package->name,
            'plan_type'      => $package->plan_type,
            'price_per_sms'  => $pricePerSms,
            'sms_count'      => $smsCount,
            'amount'         => $amount,
            'paid_amount'    => 0,
            'payment_method' => 'dgepay',
            'status'         => 'unpaid',
            'due_date'       => now(),
            'created_by'     => 'user',
        ]);

        // Notify Admins
        AppNotification::sendToAdmins(
            'New Order Placed: #' . $orderNumber,
            "{$user->name} placed an order for {$package->name} worth ৳" . number_format($amount, 2) . ' via DGePay.',
            'order',
            route('admin.invoices.index'),
            'fas fa-shopping-cart text-primary'
        );

        // Initiate payment via DGePay Gateway API
        $callbackUrl = config('services.dgepay.callback_url') ?: route('dgepay.callback');

        $result = $this->dgepayService->initiatePayment(
            amount: $amount,
            orderId: $orderNumber,
            redirectUrl: $callbackUrl,
            description: "Package Purchase: {$package->name}",
            userReference: $user->id,
            metadata: [
                'custom_field_1' => $package->name,
                'custom_field_2' => $user->email,
                'custom_field_3' => (string) $order->id,
            ]
        );

        if (!empty($result['success']) && !empty($result['payment_url'])) {
            $payment->update([
                'payment_ref_id' => $result['transaction_id'] ?? $orderNumber,
            ]);

            return redirect()->away($result['payment_url']);
        }

        // Mark as failed if gateway rejected
        $order->update(['status' => 'failed', 'payment_status' => 'failed']);
        $payment->update(['status' => 'failed']);

        return redirect()->route('user.buy_package')->with(
            'error',
            'DGePay Gateway Error: ' . ($result['message'] ?? 'Could not initiate payment. Please try again.')
        );
    }

    /**
     * Handle return callback from DGePay after payment completion/cancellation.
     */
    public function callback(Request $request)
    {
        Log::info('DGePay Callback Raw Query:', $request->all());

        $params = [];
        $rawData = $request->query('data');

        if ($rawData) {
            $params = $this->dgepayService->decryptCallbackData($rawData) ?? [];
            Log::info('DGePay Decrypted Callback:', $params);
        }

        if (empty($params)) {
            $params = $request->all();
        }

        $orderNumber = $params['unique_txn_id'] ?? $request->query('unique_txn_id');
        $statusCode  = (string) ($params['status_code'] ?? $request->query('status_code', ''));
        $status      = strtolower((string) ($params['status'] ?? $request->query('status', '')));
        $trxNumber   = $params['txn_number'] ?? ($params['txn_id'] ?? ($params['third_party_txn_number'] ?? null));
        $message     = $params['message'] ?? '';

        // Find Order & Payment
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order && !empty($params['metadata']['custom_field_3'])) {
            $order = Order::find($params['metadata']['custom_field_3']);
        }

        $payment = null;
        if ($order) {
            $payment = Payment::where('order_id', $order->id)->first();
        } elseif ($orderNumber) {
            $payment = Payment::where('payment_ref_id', $orderNumber)->first();
            if ($payment) {
                $order = $payment->order;
            }
        }

        // Check if Status is Success (DGePay Status Code 3 = Success)
        $isSuccess = ($statusCode === '3' || in_array($status, ['success', 'completed', '1'], true));

        // If not explicit success, verify status with gateway API check
        if (!$isSuccess && $orderNumber) {
            $statusCheck = $this->dgepayService->checkTransactionStatus($orderNumber);
            if (!empty($statusCheck['success']) && !empty($statusCheck['data'])) {
                $checkData = $statusCheck['data'];
                if ((string) ($checkData['status_code'] ?? '') === '3') {
                    $isSuccess = true;
                    $trxNumber = $checkData['txn_number'] ?? ($checkData['third_party_txn_number'] ?? $trxNumber);
                }
            }
        }

        if ($isSuccess && $order) {
            DB::transaction(function () use ($order, $payment, $trxNumber, $params) {
                if ($payment) {
                    $payment->update([
                        'trx_id'        => $trxNumber,
                        'status'        => 'success',
                        'response_data' => $params,
                        'paid_at'       => now(),
                    ]);
                }

                $order->update([
                    'payment_status' => 'completed',
                    'status'         => 'completed',
                ]);

                if ($order->invoice) {
                    $order->invoice->update([
                        'status'      => 'paid',
                        'paid_amount' => $order->amount,
                        'paid_at'     => now(),
                        'trx_id'      => $trxNumber,
                    ]);
                }

                // Credit user SMS balance
                if ($order->user && $order->sms_count > 0) {
                    $order->user->increment('sms_balance', $order->sms_count);
                }

                // Notify User
                AppNotification::sendToUser(
                    $order->user_id,
                    'Payment Successful: #' . $order->order_number,
                    "Your payment of ৳" . number_format($order->amount, 2) . " via DGePay was completed successfully. " . number_format($order->sms_count) . " SMS added to your account.",
                    'invoice',
                    route('user.invoices.show', $order->invoice->id ?? $order->id),
                    'fas fa-check-circle text-success'
                );
            });

            return redirect()->route('user.payment_history')->with(
                'success',
                'Payment of ৳' . number_format($order->amount, 2) . ' completed successfully via DGePay! SMS balance added.'
            );
        }

        // Cancelled or Failed
        $isCancelled = ($statusCode === '8' || in_array($status, ['cancel', 'cancelled', 'aborted'], true));

        if ($payment) {
            $payment->update([
                'status'        => $isCancelled ? 'cancelled' : 'failed',
                'response_data' => $params,
            ]);
        }

        if ($order) {
            $order->update([
                'payment_status' => $isCancelled ? 'cancelled' : 'failed',
                'status'         => $isCancelled ? 'cancelled' : 'failed',
            ]);
        }

        if ($isCancelled) {
            return redirect()->route('user.orders')->with('error', 'Payment was cancelled.');
        }

        return redirect()->route('user.orders')->with(
            'error',
            'DGePay Payment ' . ($message ?: ($status ?: 'failed')) . '. Please try again if you wish.'
        );
    }
}
