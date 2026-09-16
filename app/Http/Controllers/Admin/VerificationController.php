<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $verifications = VerificationRequest::orderBy('created_at', 'desc')->get();
        return view('admin.verifications.index', compact('verifications'));
    }

    public function show($id)
    {
        $verification = VerificationRequest::findOrFail($id);
        return response()->json($verification);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'nid_front'     => 'required|mimes:jpeg,png,jpg,pdf|max:3072',
            'nid_back'      => 'required|mimes:jpeg,png,jpg,pdf|max:3072',
            'trade_license' => 'nullable|mimes:jpeg,png,jpg,pdf|max:3072'
        ]);

        // Create username-based folder
        $username = str_replace(' ', '_', strtolower($request->name)) . '_' . time();
        $targetPath = public_path('assets/images/verifications/' . $username);
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $nidFrontFile = $request->file('nid_front');
        $nidFrontName = 'nid_front_' . uniqid() . '.' . $nidFrontFile->getClientOriginalExtension();
        $nidFrontFile->move($targetPath, $nidFrontName);
        $nidFrontPath = 'assets/images/verifications/' . $username . '/' . $nidFrontName;

        $nidBackFile = $request->file('nid_back');
        $nidBackName = 'nid_back_' . uniqid() . '.' . $nidBackFile->getClientOriginalExtension();
        $nidBackFile->move($targetPath, $nidBackName);
        $nidBackPath = 'assets/images/verifications/' . $username . '/' . $nidBackName;

        $tradeLicensePath = null;
        if ($request->hasFile('trade_license')) {
            $tradeLicenseFile = $request->file('trade_license');
            $tradeLicenseName = 'trade_license_' . uniqid() . '.' . $tradeLicenseFile->getClientOriginalExtension();
            $tradeLicenseFile->move($targetPath, $tradeLicenseName);
            $tradeLicensePath = 'assets/images/verifications/' . $username . '/' . $tradeLicenseName;
        }

        $verification = VerificationRequest::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'nid_front'     => $nidFrontPath,
            'nid_back'      => $nidBackPath,
            'trade_license' => $tradeLicensePath,
            'status'        => 'pending'
        ]);

        // Sync with User table if user already exists
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'nid_front'     => $nidFrontPath,
                'nid_back'      => $nidBackPath,
                'trade_license' => $tradeLicensePath,
                'kyc_status'    => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification request submitted successfully!'
        ]);
    }

    public function approve($id)
    {
        $verification = VerificationRequest::findOrFail($id);
        $verification->update(['status' => 'approved']);

        // Activate corresponding User account
        $user = User::where('email', $verification->email)->first();
        if ($user) {
            $user->update([
                'status'     => true, // Active user
                'kyc_status' => 'approved',
                'rejection_reason' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Verification approved and user account activated successfully!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $verification = VerificationRequest::findOrFail($id);
        $verification->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Update corresponding User account status
        $user = User::where('email', $verification->email)->first();
        if ($user) {
            $user->update([
                'status'           => false,
                'kyc_status'       => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);
        }

        return redirect()->back()->with('success', 'Verification request rejected.');
    }

    public function destroy($id)
    {
        $verification = VerificationRequest::findOrFail($id);

        if ($verification->nid_front && file_exists(public_path($verification->nid_front))) {
            unlink(public_path($verification->nid_front));
        }
        if ($verification->nid_back && file_exists(public_path($verification->nid_back))) {
            unlink(public_path($verification->nid_back));
        }
        if ($verification->trade_license && file_exists(public_path($verification->trade_license))) {
            unlink(public_path($verification->trade_license));
        }

        $verification->delete();

        return redirect()->back()->with('success', 'Verification request deleted!');
    }
}
