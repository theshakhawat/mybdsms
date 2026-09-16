<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function login()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login request.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to login with email (admin@mybdsms.com)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_users' => \App\Models\User::count(),
            'pending_verifications' => \App\Models\VerificationRequest::where('status', 'pending')->count(),
            'total_sms' => 0, // You can update this when you have SMS logs
            'delivery_rate' => '99.9%',
            'pending_contacts' => \App\Models\ContactMessage::where('is_read', false)->count(),
            'banners' => \App\Models\Banner::active()->count(),
            'services' => \App\Models\Service::active()->count(),
            'pricing_plans' => \App\Models\PricingPlan::active()->count(),
            'testimonials' => \App\Models\Testimonial::active()->count(),
        ];

        // Get recent contact messages
        $recentContacts = \App\Models\ContactMessage::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Create recent activity
        $recentActivity = [
            [
                'message' => 'New contact message received',
                'icon' => 'fa-envelope',
                'time' => \Carbon\Carbon::now()->diffForHumans(),
            ],
        ];

        if ($recentContacts->count() > 0) {
            $recentActivity[] = [
                'message' => $recentContacts->count() . ' total contact messages',
                'icon' => 'fa-comments',
                'time' => 'Today',
            ];
        }

        $recentActivity[] = [
            'message' => $stats['services'] . ' active services',
            'icon' => 'fa-concierge-bell',
            'time' => 'Updated',
        ];

        $recentActivity[] = [
            'message' => $stats['testimonials'] . ' client testimonials',
            'icon' => 'fa-quote-left',
            'time' => 'Available',
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentContacts',
            'recentActivity'
        ));
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}
