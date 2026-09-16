<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::ordered()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json($banner);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $data = $request->only(['title', 'badge', 'subtitle', 'button_text', 'button_link']);

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Create directory
                $directory = public_path('assets/images/banners');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Move file to directory
                $file->move($directory, $filename);

                // Save path without 'public/' - will use asset() helper
                $data['image'] = 'assets/images/banners/' . $filename;
            }

            $data['is_active'] = true;
            $data['sort_order'] = Banner::max('sort_order') + 1;

            Banner::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $banner = Banner::findOrFail($id);
        $data = $request->only(['title', 'badge', 'subtitle', 'button_text', 'button_link']);

        try {
            if ($request->hasFile('image')) {
                // Delete old image
                if ($banner->image) {
                    $oldImagePath = public_path($banner->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $file = $request->file('image');

                // Create directory if it doesn't exist
                $directory = public_path('assets/images/banners');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Move file to directory
                $file->move($directory, $filename);

                // Save path
                $data['image'] = 'assets/images/banners/' . $filename;
            }

            $banner->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Delete image
        if ($banner->image) {
            $imagePath = public_path($banner->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner status updated successfully'
        ]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $index => $item) {
            Banner::where('id', $item['id'])->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banners reordered successfully'
        ]);
    }
}
