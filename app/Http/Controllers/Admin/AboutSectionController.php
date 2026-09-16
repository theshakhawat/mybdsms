<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutSectionController extends Controller
{
    public function index()
    {
        $about = AboutSection::getContent();
        return view('admin.about.index', compact('about'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $about = AboutSection::getContent();
            $data = $request->only(['title', 'badge', 'subtitle', 'description', 'features']);

            if ($request->hasFile('image')) {
                // Delete old image
                if ($about->image) {
                    $oldImagePath = public_path($about->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');

                // Create directory
                $directory = public_path('assets/images/about');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store file
                $image->move($directory, $filename);
                $data['image'] = 'assets/images/about/' . $filename;
            }

            $about->update($data);

            return response()->json([
                'success' => true,
                'message' => 'About section updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}
