<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricingPlanController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::ordered()->get();
        return view('admin.pricing.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:50',
            'min_order_amount' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'button_link' => 'nullable|url|max:255',
            'features' => 'required|array',
            'icon' => 'nullable|string|max:50',
            'plan_type' => 'required|in:non-masking,masking',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Ensure features are stored as a proper JSON array (not object with numeric keys)
        $plan = PricingPlan::create([
            'name' => $request->name,
            'price' => $request->price,
            'min_order_amount' => $request->min_order_amount ?? '500',
            'description' => $request->description,
            'button_link' => $request->button_link,
            'features' => array_values($request->features), // Re-index array to ensure it's a JSON array
            'icon' => $request->icon,
            'plan_type' => $request->plan_type,
            'sort_order' => PricingPlan::max('sort_order') + 1,
        ]);

        // Notify all users about the new SMS plan
        AppNotification::sendToAllUsers(
            "New SMS Plan: {$plan->name}",
            "A new SMS package '{$plan->name}' is now available at {$plan->price}/SMS. Check it out!",
            'plan',
            route('user.packages'),
            'fas fa-tags text-success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan created successfully',
            'data' => $plan
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|string|max:50',
            'min_order_amount' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'button_link' => 'nullable|url|max:255',
            'features' => 'required|array',
            'icon' => 'nullable|string|max:50',
            'plan_type' => 'required|in:non-masking,masking',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $plan = PricingPlan::findOrFail($id);

        // Ensure features are stored as a proper JSON array (not object with numeric keys)
        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'min_order_amount' => $request->min_order_amount ?? '500',
            'description' => $request->description,
            'button_link' => $request->button_link,
            'features' => array_values($request->features), // Re-index array to ensure it's a JSON array
            'icon' => $request->icon,
            'plan_type' => $request->plan_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan updated successfully',
            'data' => $plan
        ]);
    }

    public function destroy($id)
    {
        $plan = PricingPlan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan deleted successfully'
        ]);
    }

    public function toggleFeatured($id)
    {
        $plan = PricingPlan::findOrFail($id);
        $plan->is_featured = !$plan->is_featured;
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan featured status updated',
            'data' => $plan
        ]);
    }

    public function toggleStatus($id)
    {
        $plan = PricingPlan::findOrFail($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pricing plan status updated',
            'data' => $plan
        ]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $index => $id) {
            PricingPlan::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pricing plans reordered successfully'
        ]);
    }
}
