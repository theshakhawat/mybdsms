<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    public function index()
    {
        $faqs = FAQ::ordered()->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $faq = FAQ::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'sort_order' => FAQ::max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'data' => $faq
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $faq = FAQ::findOrFail($id);
        $faq->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'data' => $faq
        ]);
    }

    public function destroy($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->is_active = !$faq->is_active;
        $faq->save();

        return response()->json([
            'success' => true,
            'message' => 'FAQ status updated successfully',
            'data' => $faq
        ]);
    }

    public function reorder(Request $request)
    {
        foreach ($request->items as $index => $id) {
            FAQ::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FAQs reordered successfully'
        ]);
    }
}
