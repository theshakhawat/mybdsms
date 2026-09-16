<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Models\Banner;
use App\Models\ContactMessage;
use App\Models\FAQ;
use App\Models\Feature;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\StatsSection;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    /**
     * Display the home page with all dynamic data
     */
    public function index()
    {
        $settings = SiteSetting::getSettings();
        $banners = Banner::active()->ordered()->get();
        $about = AboutSection::getContent();
        $stats = StatsSection::getContent();
        $services = Service::active()->ordered()->get();
        $features = Feature::active()->ordered()->get();
        $pricingPlans = PricingPlan::active()->ordered()->get();
        $testimonials = Testimonial::active()->ordered()->get();
        $faqs = FAQ::active()->ordered()->get();

        return view('home', compact(
            'settings',
            'banners',
            'about',
            'stats',
            'services',
            'features',
            'pricingPlans',
            'testimonials',
            'faqs'
        ));
    }

    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service' => 'required|string|max:100',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        ContactMessage::create($request->all());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
