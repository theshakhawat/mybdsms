<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::ordered()->get();
        return view('admin.features.index', compact('features'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $feature = Feature::create([
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => Feature::max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feature created successfully',
            'data' => $feature
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $feature = Feature::findOrFail($id);
        $feature->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Feature updated successfully',
            'data' => $feature
        ]);
    }

    public function destroy($id)
    {
        $feature = Feature::findOrFail($id);
        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $feature = Feature::findOrFail($id);
        $feature->is_active = !$feature->is_active;
        $feature->save();

        return response()->json([
            'success' => true,
            'message' => 'Feature status updated successfully',
            'data' => $feature
        ]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $index => $id) {
            Feature::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Features reordered successfully'
        ]);
    }
}
