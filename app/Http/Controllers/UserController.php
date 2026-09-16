<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Log;

class UserController extends Controller
{
    /**
     * Show User Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $kyc = VerificationRequest::where('email', $user->email)->latest()->first();
        $plans = PricingPlan::active()->ordered()->take(3)->get();
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Payment::where('user_id', $user->id)->where('status', 'success')->sum('amount');
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        $totalInvoices = Invoice::where('user_id', $user->id)->count();
        $unpaidInvoices = Invoice::where('user_id', $user->id)->where('status', 'unpaid')->count();

        return view('user.dashboard', compact('user', 'kyc', 'plans', 'totalOrders', 'totalSpent', 'recentOrders', 'totalInvoices', 'unpaidInvoices'));
    }

    /**
     * KYC Page
     */
    public function kyc()
    {
        $user = Auth::user();
        $kyc = VerificationRequest::where('email', $user->email)->latest()->first();
        return view('user.kyc', compact('user', 'kyc'));
    }

    /**
     * Packages Page
     */
    public function packages()
    {
        $plans = PricingPlan::active()->ordered()->get();
        return view('user.packages', compact('plans'));
    }

    /**
     * Buy Package Page
     */
    public function buyPackage(Request $request)
    {
        $plans = PricingPlan::active()->ordered()->get();
        $selectedPlanId = $request->query('plan');
        $siteSettings = SiteSetting::getSettings();
        return view('user.buy_package', compact('plans', 'selectedPlanId', 'siteSettings'));
    }

    /**
     * Submit Manual Payment (Bank or Office Cash)
     */
    public function submitManualPayment(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to purchase a package.');
        }

        $request->validate([
            'package_id' => 'required|exists:pricing_plans,id',
            'payment_method' => 'required|in:bank,office_cash',
            'sender_bank_name' => 'nullable|string|max:100',
            'sender_account' => 'nullable|string|max:100',
            'trx_id' => 'nullable|string|max:100',
            'slip_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:4096',
            'customer_notes' => 'nullable|string|max:1000',
        ]);

        $package = PricingPlan::where('id', $request->package_id)->where('is_active', true)->firstOrFail();

        $amount = (float) ($package->min_order_amount > 0 ? $package->min_order_amount : 500);
        $pricePerSms = (float) preg_replace('/[^0-9.]/', '', $package->price);
        $smsCount = ($pricePerSms > 0) ? (int) floor($amount / $pricePerSms) : 0;

        $orderNumber = 'ORD' . strtoupper(Str::random(10));
        $invoiceNumber = Invoice::generateInvoiceNumber();

        // Handle slip image upload if present
        $slipPath = null;
        if ($request->hasFile('slip_image')) {
            $slipFile = $request->file('slip_image');
            $filename = 'slip_' . time() . '_' . uniqid() . '.' . $slipFile->getClientOriginalExtension();
            $directory = public_path('uploads/slips');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $slipFile->move($directory, $filename);
            $slipPath = 'uploads/slips/' . $filename;
        }

        // Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'order_number' => $orderNumber,
            'package_name' => $package->name,
            'plan_type' => $package->plan_type,
            'price_per_sms' => $pricePerSms,
            'amount' => $amount,
            'sms_count' => $smsCount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Create Payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'gateway' => $request->payment_method,
            'trx_id' => $request->trx_id,
            'amount' => $amount,
            'charge' => 0.00,
            'currency' => 'BDT',
            'status' => 'pending',
            'response_data' => [
                'sender_bank_name' => $request->sender_bank_name,
                'sender_account' => $request->sender_account,
                'slip_image' => $slipPath,
                'customer_notes' => $request->customer_notes,
            ],
        ]);

        // Create Invoice
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'plan_type' => $package->plan_type,
            'price_per_sms' => $pricePerSms,
            'sms_count' => $smsCount,
            'amount' => $amount,
            'paid_amount' => 0.00,
            'payment_method' => $request->payment_method,
            'status' => 'unpaid',
            'due_date' => now()->addDays(3),
            'sender_bank_name' => $request->sender_bank_name,
            'sender_account' => $request->sender_account,
            'trx_id' => $request->trx_id,
            'slip_image' => $slipPath,
            'customer_notes' => $request->customer_notes,
            'created_by' => 'user',
        ]);

        // Notify Admins about new manual payment
        $methodLabel = $request->payment_method === 'bank' ? 'Bank Transfer' : 'Office Cash';
        $icon = $request->payment_method === 'bank' ? 'fas fa-university text-primary' : 'fas fa-money-bill-wave text-success';
        AppNotification::sendToAdmins(
            "New Manual Order: #{$orderNumber}",
            "{$user->name} submitted a {$methodLabel} order for {$package->name} worth ৳" . number_format($amount, 2) . '.',
            'order',
            route('admin.invoices.index'),
            $icon
        );

        $methodName = $request->payment_method === 'bank' ? 'ব্যাংক ট্রান্সফার' : 'অফিস ক্যাশ';
        return redirect()->route('user.invoices.show', $invoice->id)
            ->with('success', "আপনার {$methodName} পেমেন্ট রিকোয়েস্ট সফলভাবে জমা হয়েছে! অ্যাডমিন যাচাই করে প্যাকেজটি অ্যাক্টিভ করবেন।");
    }

    /**
     * User Invoices Listing
     */
    public function invoices(Request $request)
    {
        $query = Invoice::where('user_id', Auth::id());

        if ($request->filled('status') && in_array($request->status, ['paid', 'unpaid', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();
        return view('user.invoices.index', compact('invoices'));
    }

    /**
     * Show User Invoice
     */
    public function showInvoice($id)
    {
        $invoice = Invoice::where('user_id', Auth::id())
            ->with(['user', 'order', 'package'])
            ->findOrFail($id);

        $siteSettings = SiteSetting::getSettings();
        return view('user.invoices.show', compact('invoice', 'siteSettings'));
    }

    /**
     * Orders Page
     */
     public function orders()
     {
         $orders = \App\Models\Order::where('user_id', Auth::id())
             ->with('package')
             ->latest()
             ->paginate(15);
         return view('user.orders', compact('orders'));
     }

    /**
     * Payment History Page
     */
     public function paymentHistory()
     {
         $payments = \App\Models\Payment::where('user_id', Auth::id())
             ->with('order')
             ->latest()
             ->paginate(15);
         return view('user.payment_history', compact('payments'));
     }

    /**
     * My Account Page
     */
    public function account()
    {
        $user = Auth::user();
        return view('user.account', compact('user'));
    }

    /**
     * Profile Page
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Nagad Callback
     */
    public function nagodCallback(Request $request)
    {
        // Handle the callback from Nagad payment gateway
        // You can access the request data using $request->input('parameter_name')
        // For example, you can log the callback data for debugging purposes
        Log::info('Nagad Callback:', $request->all()); 
    }
}
