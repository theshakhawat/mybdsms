<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::ordered()->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'message' => 'required|string',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $imagePath = null;
            if ($request->hasFile('client_image')) {
                $image = $request->file('client_image');

                // Create directory
                $directory = public_path('assets/images/testimonials');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Move file to directory
                $image->move($directory, $filename);
                $imagePath = 'assets/images/testimonials/' . $filename;
            }

            $testimonial = Testimonial::create([
                'client_name' => $request->client_name,
                'client_position' => $request->client_position,
                'client_company' => $request->client_company,
                'message' => $request->message,
                'client_image' => $imagePath,
                'sort_order' => Testimonial::max('sort_order') + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Testimonial created successfully',
                'data' => $testimonial
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
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'message' => 'required|string',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $testimonial = Testimonial::findOrFail($id);

            // Handle image upload
            if ($request->hasFile('client_image')) {
                // Delete old image if exists
                if ($testimonial->client_image) {
                    $oldImagePath = public_path($testimonial->client_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('client_image');

                // Create directory
                $directory = public_path('assets/images/testimonials');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Move file to directory
                $image->move($directory, $filename);
                $testimonial->client_image = 'assets/images/testimonials/' . $filename;
            }

            $testimonial->client_name = $request->client_name;
            $testimonial->client_position = $request->client_position;
            $testimonial->client_company = $request->client_company;
            $testimonial->message = $request->message;
            $testimonial->save();

            return response()->json([
                'success' => true,
                'message' => 'Testimonial updated successfully',
                'data' => $testimonial
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
        $testimonial = Testimonial::findOrFail($id);

        // Delete image if exists
        if ($testimonial->client_image) {
            $imagePath = public_path($testimonial->client_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->is_active = !$testimonial->is_active;
        $testimonial->save();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial status updated successfully',
            'data' => $testimonial
        ]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $index => $id) {
            Testimonial::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Testimonials reordered successfully'
        ]);
    }
}
