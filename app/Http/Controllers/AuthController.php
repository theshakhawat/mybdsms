<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Show user login page
     */
    public function login()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle user login request
     */
    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email address is required.',
            'email.email'       => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $user = Auth::user();

            // Check if account status is active
            if (!$user->status) {
                Auth::logout();

                if ($user->kyc_status === 'pending') {
                    return back()->withErrors([
                        'email' => 'Your account is pending admin verification. You will be activated once your documents are approved.',
                    ])->withInput($request->only('email', 'remember'));
                } elseif ($user->kyc_status === 'rejected') {
                    return back()->withErrors([
                        'email' => 'Your account verification was rejected. Reason: ' . ($user->rejection_reason ?? 'Invalid documents') . '. Please contact support.',
                    ])->withInput($request->only('email', 'remember'));
                }

                return back()->withErrors([
                    'email' => 'Your account has been deactivated/suspended. Please contact support.',
                ])->withInput($request->only('email', 'remember'));
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('user.dashboard')->with('success', 'Login successful! Welcome back, ' . $user->name . '.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Show user registration page
     */
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle user registration request with KYC documents
     */
    public function registerPost(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'phone'         => 'required|string|max:20',
            'password'      => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'nid_front'     => 'required|mimes:jpeg,png,jpg,pdf|max:3072',
            'nid_back'      => 'required|mimes:jpeg,png,jpg,pdf|max:3072',
            'trade_license' => 'nullable|mimes:jpeg,png,jpg,pdf|max:3072',
        ], [
            'name.required'          => 'Full name is required.',
            'email.required'         => 'Email address is required.',
            'email.unique'           => 'This email address is already registered.',
            'phone.required'         => 'Phone number is required.',
            'password.required'      => 'Password is required.',
            'password.min'           => 'Password must be at least 8 characters long.',
            'password.confirmed'     => 'Password confirmation does not match.',
            'nid_front.required'     => 'NID front side document is required.',
            'nid_back.required'      => 'NID back side document is required.',
            'profile_image.image'    => 'Profile photo must be an image file.',
        ]);

        // Upload folder
        $username = str_replace(' ', '_', strtolower($validated['name'])) . '_' . time();
        $targetPath = public_path('assets/images/users/' . $username);
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        // Profile Image
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $name = 'avatar_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($targetPath, $name);
            $profileImagePath = 'assets/images/users/' . $username . '/' . $name;
        }

        // NID Front
        $nidFrontFile = $request->file('nid_front');
        $nidFrontName = 'nid_front_' . uniqid() . '.' . $nidFrontFile->getClientOriginalExtension();
        $nidFrontFile->move($targetPath, $nidFrontName);
        $nidFrontPath = 'assets/images/users/' . $username . '/' . $nidFrontName;

        // NID Back
        $nidBackFile = $request->file('nid_back');
        $nidBackName = 'nid_back_' . uniqid() . '.' . $nidBackFile->getClientOriginalExtension();
        $nidBackFile->move($targetPath, $nidBackName);
        $nidBackPath = 'assets/images/users/' . $username . '/' . $nidBackName;

        // Trade License
        $tradeLicensePath = null;
        if ($request->hasFile('trade_license')) {
            $tradeLicenseFile = $request->file('trade_license');
            $tradeLicenseName = 'trade_license_' . uniqid() . '.' . $tradeLicenseFile->getClientOriginalExtension();
            $tradeLicenseFile->move($targetPath, $tradeLicenseName);
            $tradeLicensePath = 'assets/images/users/' . $username . '/' . $tradeLicenseName;
        }

        // Create User (status: false, kyc_status: pending)
        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'password'      => Hash::make($validated['password']),
            'role'          => 'user',
            'status'        => false, // Inactive until admin verification
            'profile_image' => $profileImagePath,
            'nid_front'     => $nidFrontPath,
            'nid_back'      => $nidBackPath,
            'trade_license' => $tradeLicensePath,
            'kyc_status'    => 'pending',
        ]);

        // Also record into VerificationRequest table for unified admin verification management
        VerificationRequest::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'nid_front'     => $nidFrontPath,
            'nid_back'      => $nidBackPath,
            'trade_license' => $tradeLicensePath,
            'status'        => 'pending',
        ]);

        // Notify Admins about new registration
        AppNotification::sendToAdmins(
            'New User Registered',
            "{$user->name} ({$user->email}) has registered and submitted documents for verification.",
            'user_register',
            route('admin.users.index'),
            'fas fa-user-plus text-info'
        );

        return redirect()->route('user.login')->with('success', 'Registration submitted successfully! Your account will be activated once the admin reviews and approves your KYC documents.');
    }

    /**
     * Handle user logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('user.login')->with('success', 'You have been logged out successfully.');
    }
}
