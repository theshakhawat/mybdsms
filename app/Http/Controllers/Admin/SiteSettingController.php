<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::getSettings();
        $settingsFlat = $settings->toArray();

        return view('admin.settings.index', compact('settings', 'settingsFlat'));
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_logo' => 'nullable|string',
            'company_address' => 'nullable|string',
            'company_email' => 'nullable|email',
            'company_phone' => 'nullable|string|max:50',
            'company_hours' => 'nullable|string|max:100',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_linkedin' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'account_panel_url' => 'nullable|url',
            'sms_panel_url' => 'nullable|url',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_branch' => 'nullable|string|max:100',
            'bank_routing_number' => 'nullable|string|max:50',
            'bank_instructions' => 'nullable|string',
            'office_payment_instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $settings = SiteSetting::updateSettings($request->only([
            'company_logo',
            'company_address',
            'company_email',
            'company_phone',
            'company_hours',
            'social_facebook',
            'social_twitter',
            'social_linkedin',
            'social_youtube',
            'account_panel_url',
            'sms_panel_url',
            'bank_name',
            'bank_account_name',
            'bank_account_number',
            'bank_branch',
            'bank_routing_number',
            'bank_instructions',
            'office_payment_instructions',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Create directory in public/assets/images/settings
                $directory = public_path('assets/images/settings');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Store file
                $image->move($directory, $filename);
                $path = 'assets/images/settings/' . $filename;

                $url = asset($path);

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'path' => $url
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image uploaded'
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ensure the storage link exists for public access
     */
    private function ensureStorageLinkExists()
    {
        $publicStoragePath = public_path('storage');

        // Create the symbolic link if it doesn't exist
        if (!file_exists($publicStoragePath)) {
            try {
                symlink(storage_path('app/public'), $publicStoragePath);
            } catch (\Exception $e) {
                \Log::error('Could not create storage link: ' . $e->getMessage());
                // Try to create the directory structure as fallback
                if (!file_exists($publicStoragePath)) {
                    mkdir($publicStoragePath, 0755, true);
                }
            }
        }
    }
}
