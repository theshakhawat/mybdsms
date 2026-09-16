<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with search, filter, and pagination
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status === '1' || $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Get specific user details for editing via Ajax
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,user',
            'status'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => $request->has('status') ? (bool)$request->status : true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data'    => $user
        ]);
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role'     => 'required|in:admin,user',
            'status'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $newStatus = $request->has('status') ? (bool)$request->status : $user->status;
        $userData = [
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $request->role,
            'status' => $newStatus,
        ];

        if ($newStatus && $user->kyc_status === 'pending') {
            $userData['kyc_status'] = 'approved';
            \App\Models\VerificationRequest::where('email', $user->email)->update(['status' => 'approved']);
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data'    => $user
        ]);
    }

    /**
     * Toggle user active/inactive status
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deactivating themselves
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status.'
            ], 403);
        }

        $user->status = !$user->status;
        if ($user->status && $user->kyc_status === 'pending') {
            $user->kyc_status = 'approved';
            \App\Models\VerificationRequest::where('email', $user->email)->update(['status' => 'approved']);
        }
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'status'  => $user->status,
            'kyc_status' => $user->kyc_status
        ]);
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent self deletion
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}

