<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::ordered()->get();
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $service = Service::create([
            'title' => $request->title,
            'icon' => $request->icon,
            'description' => $request->description,
            'sort_order' => Service::max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $service = Service::findOrFail($id);
        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service status updated successfully',
            'data' => $service
        ]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $index => $id) {
            Service::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Services reordered successfully'
        ]);
    }
}
