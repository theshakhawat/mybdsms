<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with filters
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['user', 'order', 'package']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['paid', 'unpaid', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Search by invoice number, trx_id, or user name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('trx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Metrics calculations
        $totalInvoices = Invoice::count();
        $totalPaidAmount = Invoice::where('status', 'paid')->sum('amount');
        $totalUnpaidAmount = Invoice::where('status', 'unpaid')->sum('amount');
        $unpaidCount = Invoice::where('status', 'unpaid')->count();

        $invoices = $query->latest()->paginate(15)->withQueryString();
        $users = User::where('role', 'user')->orderBy('name')->get();
        $plans = PricingPlan::active()->ordered()->get();

        return view('admin.invoices.index', compact(
            'invoices',
            'users',
            'plans',
            'totalInvoices',
            'totalPaidAmount',
            'totalUnpaidAmount',
            'unpaidCount'
        ));
    }

    /**
     * Store a newly created invoice (manual admin billing)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'billing_type' => 'required|in:package,custom',
            'package_id' => 'nullable|required_if:billing_type,package|exists:pricing_plans,id',
            'custom_title' => 'nullable|required_if:billing_type,custom|string|max:255',
            'plan_type' => 'nullable|string|max:50',
            'sms_count' => 'nullable|numeric|min:0',
            'price_per_sms' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:bank,office_cash,nagad,other',
            'status' => 'required|in:paid,unpaid',
            'due_date' => 'nullable|date',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = (float) $request->amount;
        $status = $request->status;
        $dueDate = $request->due_date ? $request->due_date : now()->addDays(7);

        if ($request->billing_type === 'package') {
            $package = PricingPlan::findOrFail($request->package_id);
            $packageName = $package->name;
            $planType = $package->plan_type;
            $pricePerSms = (float) preg_replace('/[^0-9.]/', '', $package->price);
            $smsCount = ($pricePerSms > 0) ? (int) floor($amount / $pricePerSms) : 0;
            $packageId = $package->id;
        } else {
            $packageName = $request->custom_title ?: 'Custom SMS Billing';
            $planType = $request->plan_type ?: 'non-masking';
            $smsCount = (int) ($request->sms_count ?: 0);
            $pricePerSms = (float) ($request->price_per_sms ?: ($smsCount > 0 ? $amount / $smsCount : 0));
            $packageId = null;
        }

        DB::transaction(function () use ($user, $packageName, $planType, $pricePerSms, $smsCount, $amount, $packageId, $request, $status, $dueDate, &$invoice) {
            $orderNumber = 'ORD' . strtoupper(Str::random(10));
            $invoiceNumber = Invoice::generateInvoiceNumber();

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'package_id' => $packageId,
                'order_number' => $orderNumber,
                'package_name' => $packageName,
                'plan_type' => $planType,
                'price_per_sms' => $pricePerSms,
                'amount' => $amount,
                'sms_count' => $smsCount,
                'payment_method' => $request->payment_method,
                'payment_status' => $status === 'paid' ? 'completed' : 'pending',
                'status' => $status === 'paid' ? 'completed' : 'pending',
            ]);

            // Create Payment
            Payment::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'gateway' => $request->payment_method,
                'amount' => $amount,
                'charge' => 0.00,
                'currency' => 'BDT',
                'status' => $status === 'paid' ? 'success' : 'pending',
                'paid_at' => $status === 'paid' ? now() : null,
                'response_data' => ['admin_billed' => true, 'notes' => $request->admin_notes],
            ]);

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'package_id' => $packageId,
                'package_name' => $packageName,
                'plan_type' => $planType,
                'price_per_sms' => $pricePerSms,
                'sms_count' => $smsCount,
                'amount' => $amount,
                'paid_amount' => $status === 'paid' ? $amount : 0.00,
                'payment_method' => $request->payment_method,
                'status' => $status,
                'due_date' => $dueDate,
                'paid_at' => $status === 'paid' ? now() : null,
                'admin_notes' => $request->admin_notes,
                'created_by' => 'admin',
            ]);

            // Credit SMS balance if paid upon creation
            if ($status === 'paid' && $smsCount > 0) {
                $user->increment('sms_balance', $smsCount);
            }

            // Notify user about the new invoice
            AppNotification::sendToUser(
                $user->id,
                "New Invoice Generated: #{$invoiceNumber}",
                "A new invoice #{$invoiceNumber} worth ৳" . number_format($amount, 2) . " has been created for your account (" . ucfirst($status) . ").",
                'invoice',
                route('user.invoices.show', $invoice->id),
                'fas fa-file-invoice-dollar text-primary'
            );
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'ইউজারের নামে ইনভয়েস সফলভাবে তৈরি করা হয়েছে!',
                'invoice' => $invoice,
            ]);
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', 'ইউজারের নামে ইনভয়েস সফলভাবে তৈরি করা হয়েছে!');
    }

    /**
     * View Invoice details for Admin
     */
    public function show($id)
    {
        $invoice = Invoice::with(['user', 'order', 'package'])->findOrFail($id);
        $siteSettings = SiteSetting::getSettings();
        return view('admin.invoices.show', compact('invoice', 'siteSettings'));
    }

    /**
     * Update Invoice status (Paid, Unpaid, Cancelled)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:paid,unpaid,cancelled',
        ]);

        $invoice = Invoice::with(['user', 'order'])->findOrFail($id);
        $oldStatus = $invoice->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice status is already ' . $newStatus,
                'status' => $newStatus,
            ]);
        }

        DB::transaction(function () use ($invoice, $oldStatus, $newStatus) {
            $user = $invoice->user;
            $order = $invoice->order;

            if ($newStatus === 'paid') {
                $invoice->status = 'paid';
                $invoice->paid_amount = $invoice->amount;
                $invoice->paid_at = now();

                // Update Order & Payment
                if ($order) {
                    $order->update([
                        'status' => 'completed',
                        'payment_status' => 'completed',
                    ]);

                    Payment::where('order_id', $order->id)->update([
                        'status' => 'success',
                        'paid_at' => now(),
                    ]);
                }

                // Credit user SMS balance
                if ($user && $invoice->sms_count > 0) {
                    $user->increment('sms_balance', $invoice->sms_count);
                }
            } elseif ($newStatus === 'unpaid') {
                $invoice->status = 'unpaid';
                $invoice->paid_amount = 0.00;
                $invoice->paid_at = null;

                // Update Order & Payment
                if ($order) {
                    $order->update([
                        'status' => 'pending',
                        'payment_status' => 'pending',
                    ]);

                    Payment::where('order_id', $order->id)->update([
                        'status' => 'pending',
                        'paid_at' => null,
                    ]);
                }

                // Deduct SMS balance if it was previously paid
                if ($oldStatus === 'paid' && $user && $invoice->sms_count > 0) {
                    $user->decrement('sms_balance', min($user->sms_balance, $invoice->sms_count));
                }
            } elseif ($newStatus === 'cancelled') {
                $invoice->status = 'cancelled';
                $invoice->paid_at = null;

                if ($order) {
                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'cancelled',
                    ]);

                    Payment::where('order_id', $order->id)->update([
                        'status' => 'cancelled',
                    ]);
                }

                // If previously paid, deduct SMS balance
                if ($oldStatus === 'paid' && $user && $invoice->sms_count > 0) {
                    $user->decrement('sms_balance', min($user->sms_balance, $invoice->sms_count));
                }
            }

            $invoice->save();

            // Notify user about invoice status update
            $statusLabel = strtoupper($newStatus);
            $icon = $newStatus === 'paid' ? 'fas fa-check-circle text-success' : ($newStatus === 'unpaid' ? 'fas fa-clock text-warning' : 'fas fa-ban text-danger');
            AppNotification::sendToUser(
                $invoice->user_id,
                "Invoice Status Updated: {$statusLabel}",
                "Your invoice #{$invoice->invoice_number} has been marked as {$statusLabel}.",
                'invoice',
                route('user.invoices.show', $invoice->id),
                $icon
            );
        });

        $message = $newStatus === 'paid' 
            ? 'ইনভয়েস সফলভাবে পরিশোধিত (Paid) মার্ক করা হয়েছে এবং ইউজারের SMS ব্যালেন্সে যুক্ত হয়েছে!'
            : ($newStatus === 'unpaid' 
                ? 'ইনভয়েস বকেয়া (Unpaid) হিসেবে চিহ্নিত করা হয়েছে।' 
                : 'ইনভয়েস বাতিল করা হয়েছে।');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $newStatus,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete an invoice
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Deduct balance if paid
        if ($invoice->status === 'paid' && $invoice->user && $invoice->sms_count > 0) {
            $invoice->user->decrement('sms_balance', min($invoice->user->sms_balance, $invoice->sms_count));
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully',
        ]);
    }
}
