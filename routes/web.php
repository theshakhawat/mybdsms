<?php

use App\Http\Controllers\Admin\AboutSectionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PricingPlanController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StatsSectionController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DgepayController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NagadController;
use App\Http\Controllers\NagadPaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Frontend routes
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::post('/contact', [FrontendController::class, 'submitContact'])->name('contact.submit');

// // Verification routes (public - anyone can submit)
Route::post('/verification/submit', [VerificationController::class, 'submit'])->name('verification.submit');


// User Auth
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/user/login', [AuthController::class, 'login'])->name('user.login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('user.loginPost');
Route::get('/register', [AuthController::class, 'register'])->name('user.register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('user.registerPost');

// // Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Redirect admin root to login
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    // Admin Login routes (guest)
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/login', [AdminController::class, 'authenticate'])->name('authenticate');

    // Admin Protected routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        // Services management
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::put('/{id}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [ServiceController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [ServiceController::class, 'reorder'])->name('reorder');
        });

        // Features management
        Route::prefix('features')->name('features.')->group(function () {
            Route::get('/', [FeatureController::class, 'index'])->name('index');
            Route::post('/', [FeatureController::class, 'store'])->name('store');
            Route::put('/{id}', [FeatureController::class, 'update'])->name('update');
            Route::delete('/{id}', [FeatureController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [FeatureController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [FeatureController::class, 'reorder'])->name('reorder');
        });

        // Pricing plans management
        Route::prefix('pricing')->name('pricing.')->group(function () {
            Route::get('/', [PricingPlanController::class, 'index'])->name('index');
            Route::post('/', [PricingPlanController::class, 'store'])->name('store');
            Route::put('/{id}', [PricingPlanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PricingPlanController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/featured', [PricingPlanController::class, 'toggleFeatured'])->name('featured');
            Route::patch('/{id}/toggle', [PricingPlanController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [PricingPlanController::class, 'reorder'])->name('reorder');
        });

        // Testimonials management
        Route::prefix('testimonials')->name('testimonials.')->group(function () {
            Route::get('/', [TestimonialController::class, 'index'])->name('index');
            Route::post('/', [TestimonialController::class, 'store'])->name('store');
            Route::put('/{id}', [TestimonialController::class, 'update'])->name('update');
            Route::delete('/{id}', [TestimonialController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [TestimonialController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [TestimonialController::class, 'reorder'])->name('reorder');
        });

        // FAQs management
        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/', [FAQController::class, 'index'])->name('index');
            Route::post('/', [FAQController::class, 'store'])->name('store');
            Route::put('/{id}', [FAQController::class, 'update'])->name('update');
            Route::delete('/{id}', [FAQController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [FAQController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [FAQController::class, 'reorder'])->name('reorder');
        });

        // Contact messages
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [ContactMessageController::class, 'index'])->name('index');
            Route::get('/{id}', [ContactMessageController::class, 'show'])->name('show');
            Route::delete('/{id}', [ContactMessageController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/read', [ContactMessageController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [ContactMessageController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/unread-count', [ContactMessageController::class, 'getUnreadCount'])->name('unread-count');
        });

        // Site settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SiteSettingController::class, 'index'])->name('index');
            Route::post('/', [SiteSettingController::class, 'update'])->name('update');
            Route::post('/upload-image', [SiteSettingController::class, 'uploadImage'])->name('upload-image');
        });

        // Banners management
        Route::prefix('banners')->name('banners.')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->name('index');
            Route::get('/{id}', [BannerController::class, 'show'])->name('show');
            Route::post('/', [BannerController::class, 'store'])->name('store');
            Route::put('/{id}', [BannerController::class, 'update'])->name('update');
            Route::delete('/{id}', [BannerController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [BannerController::class, 'reorder'])->name('reorder');
        });

        // About section
        Route::prefix('about')->name('about.')->group(function () {
            Route::get('/', [AboutSectionController::class, 'index'])->name('index');
            Route::post('/', [AboutSectionController::class, 'update'])->name('update');
            Route::post('/upload-image', [SiteSettingController::class, 'uploadImage'])->name('upload-image');
        });

        // Stats section
        Route::prefix('stats')->name('stats.')->group(function () {
            Route::get('/', [StatsSectionController::class, 'index'])->name('index');
            Route::post('/', [StatsSectionController::class, 'update'])->name('update');
        });

        // Verifications management
        Route::prefix('verifications')->name('verifications.')->group(function () {
            Route::get('/', [VerificationController::class, 'index'])->name('index');
            Route::get('/{id}', [VerificationController::class, 'show'])->name('show');
            Route::patch('/{id}/approve', [VerificationController::class, 'approve'])->name('approve');
            Route::patch('/{id}/reject', [VerificationController::class, 'reject'])->name('reject');
            Route::delete('/{id}', [VerificationController::class, 'destroy'])->name('destroy');
        });

        // Users management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [UserManagementController::class, 'show'])->name('show');
            Route::put('/{id}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle', [UserManagementController::class, 'toggleStatus'])->name('toggle');
        });

        // Invoices management
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::post('/', [InvoiceController::class, 'store'])->name('store');
            Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [InvoiceController::class, 'updateStatus'])->name('update_status');
            Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
        });

        // Notifications management
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'adminIndex'])->name('index');
            Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read.patch');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark_all_read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });
    });
});

// Shared AJAX Notification Dropdown
Route::middleware('auth')->get('/notifications/dropdown', [NotificationController::class, 'dropdown'])->name('notifications.dropdown');

// // User Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/kyc', [UserController::class, 'kyc'])->name('kyc');
    Route::get('/packages', [UserController::class, 'packages'])->name('packages');
    Route::get('/buy-package', [UserController::class, 'buyPackage'])->name('buy_package');
    Route::post('/buy-package/manual', [UserController::class, 'submitManualPayment'])->name('manual_payment');
    Route::get('/invoices', [UserController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{id}', [UserController::class, 'showInvoice'])->name('invoices.show');
    Route::get('/orders', [UserController::class, 'orders'])->name('orders');
    Route::get('/payment-history', [UserController::class, 'paymentHistory'])->name('payment_history');
    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'userIndex'])->name('index');
        Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read.patch');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark_all_read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Nagad Payment Routes


    // পেমেন্ট শুরু করার রাউট
    Route::post('/nagad/pay', [NagadPaymentController::class, 'pay'])->name('nagad.pay');
    Route::post('/dgepay/pay', [DgepayController::class, 'pay'])->name('dgepay.pay');
});

// পেমেন্ট শেষে Nagad / DGePay যে কলব্যাক দেবে (Guest / Web accessible)
Route::get('/nagad/callback', [NagadPaymentController::class, 'callback'])->name('nagad.callback');
Route::match(['get', 'post'], '/dgepay/callback', [DgepayController::class, 'callback'])->name('dgepay.callback');

Route::get('/clear', function () {
    Artisan::call('cache:forget spatie.permission.cache');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
