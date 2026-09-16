<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatsSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatsSectionController extends Controller
{
    public function index()
    {
        $stats = StatsSection::getContent();
        return view('admin.stats.index', compact('stats'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clients_count' => 'nullable|string|max:50',
            'clients_label' => 'nullable|string|max:255',
            'sms_count' => 'nullable|string|max:50',
            'sms_label' => 'nullable|string|max:255',
            'delivery_count' => 'nullable|string|max:50',
            'delivery_label' => 'nullable|string|max:255',
            'years_count' => 'nullable|string|max:50',
            'years_label' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $stats = StatsSection::getContent();
        $data = $request->only([
            'clients_count', 'clients_label',
            'sms_count', 'sms_label',
            'delivery_count', 'delivery_label',
            'years_count', 'years_label'
        ]);

        $stats->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Stats section updated successfully'
        ]);
    }
}
