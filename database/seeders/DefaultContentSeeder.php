<?php

namespace Database\Seeders;

use App\Models\FAQ;
use App\Models\Feature;
use App\Models\PricingPlan;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DefaultContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Site Settings (empty record - admin will fill in)
        SiteSetting::firstOrCreate([]);


        // Create Services
        $services = [
            ['title' => 'Non-Masking SMS', 'icon' => 'fas fa-sms', 'description' => 'Send promotional messages using numeric sender IDs. Perfect for marketing campaigns and bulk announcements at affordable rates.', 'sort_order' => 1],
            ['title' => 'Transactional SMS', 'icon' => 'fas fa-shield-alt', 'description' => 'Send important alerts, notifications, and updates to your customers. Ensure critical information reaches them instantly.', 'sort_order' => 2],
            ['title' => 'OTP Verification', 'icon' => 'fas fa-key', 'description' => 'Secure user verification with one-time passwords. Perfect for app authentication, account security, and fraud prevention.', 'sort_order' => 3],
            ['title' => 'Promotional SMS', 'icon' => 'fas fa-bullhorn', 'description' => 'Reach thousands of customers with your marketing messages. Boost sales, announce offers, and drive engagement.', 'sort_order' => 4],
            ['title' => 'Schedule SMS', 'icon' => 'fas fa-calendar-alt', 'description' => 'Plan and schedule your campaigns in advance. Set it once and let your messages be delivered at the perfect time.', 'sort_order' => 5],
            ['title' => 'API Integration', 'icon' => 'fas fa-code', 'description' => 'Integrate SMS capabilities into your application with our robust API. Automate your messaging workflow easily.', 'sort_order' => 6],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['title' => $service['title']],
                $service
            );
        }

        // Create Features
        $features = [
            ['title' => 'Dynamic Routing', 'description' => 'Intelligent route selection ensures optimal delivery speed and reliability for every message.', 'sort_order' => 1],
            ['title' => 'Real-time Analytics', 'description' => 'Track delivery status, open rates, and campaign performance with our smart dashboard.', 'sort_order' => 2],
            ['title' => 'Load Balancing', 'description' => 'Optimizes SMS traffic across multiple routes to prevent congestion and ensure timely delivery.', 'sort_order' => 3],
            ['title' => 'Template Management', 'description' => 'Save and reuse message templates for consistent branding and faster campaign setup.', 'sort_order' => 4],
            ['title' => 'Group Messaging', 'description' => 'Segment contacts and send targeted messages to specific groups for better engagement.', 'sort_order' => 5],
            ['title' => 'Excel Upload', 'description' => 'Import contacts from Excel files for quick and easy bulk messaging campaigns.', 'sort_order' => 6],
            ['title' => 'Multilingual Support', 'description' => 'Send messages in Bengali and English to reach a wider audience effectively.', 'sort_order' => 7],
            ['title' => 'DLR Tracking', 'description' => 'Get detailed delivery receipts for every message sent through our platform.', 'sort_order' => 8],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(
                ['title' => $feature['title']],
                $feature
            );
        }

        // Create Pricing Plans
        $plans = [
            [
                'name' => 'Starter',
                'price' => '৳0.50',
                'description' => 'Perfect for small businesses',
                'features' => ['Min Order: 500 BDT', '1000 SMS Credits', 'Non-Messaging SMS', 'Web & API Access', 'Unlimited Validity', 'Email Support'],
                'icon' => 'fas fa-sms',
                'plan_type' => 'non-masking',
                'sort_order' => 1,
                'min_order_amount' => '500'
            ],
            [
                'name' => 'Professional',
                'price' => '৳0.45',
                'description' => 'Best for growing businesses',
                'features' => ['Min Order: 2250 BDT', '5000 SMS Credits', 'Non-Messaging SMS', 'Web & API Access', 'Unlimited Validity', 'Priority Support'],
                'icon' => 'fas fa-rocket',
                'plan_type' => 'non-masking',
                'is_featured' => true,
                'sort_order' => 2,
                'min_order_amount' => '2250'
            ],
            [
                'name' => 'Enterprise',
                'price' => '৳0.40',
                'description' => 'For large organizations',
                'features' => ['Min Order: 4000 BDT', '10000 SMS Credits', 'Non-Messaging SMS', 'Web & API Access', 'Unlimited Validity', 'Dedicated Account Manager'],
                'icon' => 'fas fa-building',
                'plan_type' => 'non-masking',
                'sort_order' => 3,
                'min_order_amount' => '4000'
            ],
        ];

        foreach ($plans as $plan) {
            PricingPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        // Create Testimonials
        $testimonials = [
            [
                'client_name' => 'Md. Rahim Uddin',
                'client_position' => 'CEO',
                'client_company' => 'TechMart Bangladesh',
                'message' => 'MyBDSMS has transformed how we communicate with our customers. Their API integration was seamless, and delivery rates are exceptional. Highly recommended!',
                'client_image' => 'https://i.pravatar.cc/150?img=12',
                'sort_order' => 1
            ],
            [
                'client_name' => 'Fatima Akter',
                'client_position' => 'Marketing Director',
                'client_company' => 'FashionHub',
                'message' => 'The best SMS service provider we\'ve used. Customer support is excellent, and the platform is incredibly user-friendly. Our marketing campaigns have never been more effective.',
                'client_image' => 'https://i.pravatar.cc/150?img=5',
                'sort_order' => 2
            ],
            [
                'client_name' => 'Kamal Hossain',
                'client_position' => 'CTO',
                'client_company' => 'SecureApp BD',
                'message' => 'We\'ve been using MyBDSMS for our OTP verification needs. The reliability and speed of delivery has significantly improved our user experience. Great service!',
                'client_image' => 'https://i.pravatar.cc/150?img=33',
                'sort_order' => 3
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                ['client_name' => $testimonial['client_name']],
                $testimonial
            );
        }

        // Create FAQs
        $faqs = [
            [
                'question' => 'What is Non-Masking SMS?',
                'answer' => 'Non-Masking SMS uses a numeric sender ID (like 017XXXXXXXXX) for sending messages. It\'s ideal for promotional campaigns and is very cost-effective. Recipients can see and save the number for future reference.',
                'sort_order' => 1
            ],
            [
                'question' => 'Can I send SMS in Bangla language?',
                'answer' => 'Yes! MyBDSMS supports multilingual SMS including Bangla. You can send messages in Bangla script (Unicode) to better connect with your local audience. Our platform handles both English and Bangla messages seamlessly.',
                'sort_order' => 2
            ],
            [
                'question' => 'How do I integrate SMS API into my application?',
                'answer' => 'We provide REST API with comprehensive documentation. You can easily integrate SMS functionality into any application using PHP, Python, Java, or any other programming language. Contact us for API documentation and access credentials.',
                'sort_order' => 3
            ],
            [
                'question' => 'What is the delivery guarantee for SMS?',
                'answer' => 'We maintain a 99.9% delivery rate through our direct connections with all major mobile operators in Bangladesh. Our dynamic routing system ensures messages are delivered via the most reliable path. You\'ll also receive real-time delivery reports.',
                'sort_order' => 4
            ],
            [
                'question' => 'How long do SMS credits remain valid?',
                'answer' => 'All our SMS packages come with unlimited validity! Once you purchase credits, they never expire. Use them whenever you need without any time pressure. This is one of the many benefits of choosing MyBDSMS as your SMS provider.',
                'sort_order' => 5
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}
