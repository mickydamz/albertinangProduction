<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ─── Admin Controllers ────────────────────────────────────────────────────────
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAffiliateController;
use App\Http\Controllers\AdminBrandController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminCityController;
use App\Http\Controllers\AdminColorController;
use App\Http\Controllers\AdminCouponController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminLocationController;
use App\Http\Controllers\AdminStateController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminPaymentMethodController;
use App\Http\Controllers\AdminPickupPointController;
use App\Http\Controllers\AdminStoreLocationController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminSizeController;
use App\Http\Controllers\AdminSupplierController;
use App\Http\Controllers\AdminTagController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\AdminPaystackTransactionController;
use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandAssignmentController;
use App\Http\Controllers\MarkupController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\WeightController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminInvoiceSettingsController;
use App\Http\Controllers\AdminFaqController;
use App\Http\Controllers\AdminAboutController;
use App\Http\Controllers\AdminContactController;

// ─── Manager Controllers ──────────────────────────────────────────────────────
use App\Http\Controllers\ManagerProductController;

// ─── Supplier Controllers ─────────────────────────────────────────────────────
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDashboardController;
use App\Http\Controllers\SupplierProductController;
use App\Http\Controllers\SupplierProfileController;
use App\Http\Controllers\SupplierTransactionController;

// ─── User Controllers ─────────────────────────────────────────────────────────
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserTransactionController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\TwoFactorSettingsController;

// ─── Shared / Public Controllers ──────────────────────────────────────────────
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\InstallationOptionsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AdminAuditController;
use App\Http\Controllers\AdminShippingController;
use App\Http\Controllers\PaystackWebhookController;
// ─── Models ───────────────────────────────────────────────────────────────────
use App\Models\Category;
use App\Models\Order;
use Matthewbdaly\LaravelCities\Models\City;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// ═══════════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════════════════════════

// Storefront home — the page every visitor lands on
Route::get('/',     [UserDashboardController::class, 'index'])->name('dashboard');
Route::get('/shop', [UserDashboardController::class, 'index'])->name('shop');


Route::get('/fix-pdf-now', function () {
    
    echo "<h2>🔧 Applying DomPDF Fix</h2>";
    echo "<pre style='background:#f4f4f4; padding:20px; font-family:monospace;'>";
    
    // Clear everything first
    \Artisan::call('optimize:clear');
    echo "✓ All caches cleared\n\n";
    
    // Check if class exists
    if (!class_exists('Barryvdh\DomPDF\PDF')) {
        echo "✗ DomPDF class not found. Run: composer dump-autoload\n";
        echo "</pre>";
        return;
    }
    
    echo "✓ DomPDF class found\n\n";
    
    // FORCE manual binding
    app()->singleton('dompdf.wrapper', function ($app) {
        return new \Barryvdh\DomPDF\PDF(
            $app['config'],
            $app['files'],
            $app['view']
        );
    });
    
    // Also bind as alias for Facade
    if (!app()->bound('dompdf')) {
        app()->alias('dompdf.wrapper', 'dompdf');
    }
    
    echo "✓ Manual binding applied\n\n";
    
    // Test the binding
    echo "Testing binding...\n";
    try {
        $pdf = app('dompdf.wrapper');
        echo "✓ Binding test: SUCCESS\n\n";
    } catch (\Exception $e) {
        echo "✗ Binding test failed: " . $e->getMessage() . "\n\n";
        
        // Try alternative binding
        echo "Trying alternative binding...\n";
        app()->bind('dompdf.wrapper', 'Barryvdh\DomPDF\PDF');
        try {
            $pdf = app('dompdf.wrapper');
            echo "✓ Alternative binding: SUCCESS\n\n";
        } catch (\Exception $e2) {
            echo "✗ Alternative binding also failed\n\n";
            echo "</pre>";
            return;
        }
    }
    
    // Test actual PDF generation
    echo "Testing PDF generation...\n";
    try {
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML('<h1>Test PDF</h1><p>Generated at: ' . now() . '</p>');
        $output = $pdf->output();
        
        // Save test PDF
        $path = storage_path('app/public/test-invoice.pdf');
        file_put_contents($path, $output);
        
        echo "✓ Test PDF created: storage/app/public/test-invoice.pdf\n\n";
    } catch (\Exception $e) {
        echo "✗ PDF generation failed: " . $e->getMessage() . "\n\n";
    }
    
    // Create permanent fix file
    echo "Creating permanent fix...\n";
    
    $providerPath = app_path('Providers/AppServiceProvider.php');
    $providerContent = file_get_contents($providerPath);
    
    // Check if fix already exists
    if (strpos($providerContent, 'dompdf.wrapper') !== false) {
        echo "⚠ Fix already in AppServiceProvider\n\n";
    } else {
        // Add the binding to AppServiceProvider
        $search = 'public function register()';
        $replace = 'public function register()
    {
        // DomPDF manual binding fix
        $this->app->singleton(\'dompdf.wrapper\', function ($app) {
            return new \Barryvdh\DomPDF\PDF(
                $app[\'config\'],
                $app[\'files\'],
                $app[\'view\']
            );
        });';
        
        if (strpos($providerContent, $search) !== false) {
            // Replace empty register method
            $newContent = str_replace(
                'public function register()
    {
        //
    }',
                $replace . '
    }',
                $providerContent
            );
            
            // Also handle case where register has other content
            if ($newContent === $providerContent) {
                $newContent = str_replace(
                    'public function register()
    {',
                    $replace,
                    $providerContent
                );
            }
            
            file_put_contents($providerPath, $newContent);
            echo "✓ Permanent fix added to AppServiceProvider\n\n";
        } else {
            echo "⚠ Could not auto-add fix. Add manually (see below)\n\n";
        }
    }
    
    // Now test with your actual controller code
    echo "Testing with Facade...\n";
    try {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Facade Test</h1>');
        echo "✓ Facade working!\n\n";
    } catch (\Exception $e) {
        echo "✗ Facade failed: " . $e->getMessage() . "\n\n";
    }
    
    echo "<strong>✅ Fix applied!</strong>\n\n";
    echo "Now test your invoice PDF download.\n";
    echo "If it still fails, add this manually to AppServiceProvider.php:\n\n";
    echo htmlspecialchars('
use Barryvdh\DomPDF\PDF;

public function register()
{
    $this->app->singleton(\'dompdf.wrapper\', function ($app) {
        return new PDF($app[\'config\'], $app[\'files\'], $app[\'view\']);
    });
}');
    
    echo "\n\n";
    echo '<a href="' . url('/') . '" style="padding:10px 20px; background:#4CAF50; color:white; text-decoration:none;">← Go to Site & Test PDF</a>';
    
    echo "</pre>";
});

Route::get('/about',         fn () => view('sims/about'))->name('about');
Route::get('/terms',         fn () => view('sims/terms'))->name('terms');
Route::get('/privacy',       fn () => view('sims/privacy'))->name('privacy');
Route::get('/faq',           fn () => view('sims/faq'))->name('faq');
Route::get('/blog',          fn () => view('sims/blog'))->name('blog.index');
Route::get('/store-locator', fn () => view('sims/store_locator'))->name('store');
Route::get('/store-locations', [\App\Http\Controllers\StoreLocationController::class, 'index'])->name('store.locations');

Route::get('/contact',  [ContactController::class, 'index'])->name('contact');
// Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact-form')
    ->name('contact.store');

// ── Products (public browsing) ────────────────────────────────────────────────
Route::get('/products',          [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/brand/{brandSlug}', [ProductController::class, 'showBrand'])->name('brand.show');
Route::get('/category/{categoryName}', [ProductController::class, 'showCategory'])
    ->where('categoryName', '.*')
    ->name('category.show');
Route::get('/search',              [ProductController::class, 'searchProducts'])->name('search');
Route::get('/search/{searchTerm}', [ProductController::class, 'searchProducts'])->name('product.search');
Route::get('/search-suggestions',  [ProductController::class, 'getSearchSuggestions'])->name('search.suggestions');

// ── Cart (public — guests can browse) ─────────────────────────────────────────
Route::get('/cart',                 [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}',  [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/increase/{id}', [CartController::class, 'increaseQuantity'])->name('cart.increase');
Route::patch('/cart/decrease/{id}', [CartController::class, 'decreaseQuantity'])->name('cart.decrease');
Route::delete('/cart/remove/{id}',  [CartController::class, 'removeItem'])->name('cart.remove');

// ── Public API ────────────────────────────────────────────────────────────────
// Route::get('/currencies',                [CurrencyController::class, 'publicIndex']);
// Route::get('/api/locations',             [LocationController::class, 'index']);
// Route::get('/api/pickup-points',         [LocationController::class, 'pickupPoints']);
// Route::post('/api/installation-options', [InstallationOptionsController::class, 'getForProducts']);
// Route::post('/api/coupons/validate',     [CouponController::class, 'apply'])->name('coupons.validate');

// ── Public API ────────────────────────────────────────────────────────────────
Route::get('/currencies',                [CurrencyController::class, 'publicIndex']);
Route::post('/currency/set',             [CurrencyController::class, 'setSession'])->name('currency.set');
Route::get('/api/states',                [LocationController::class, 'states']); // <-- Add this here
Route::get('/api/locations',             [LocationController::class, 'index']);
Route::get('/api/pickup-points',         [LocationController::class, 'pickupPoints']);
Route::post('/api/installation-options', [InstallationOptionsController::class, 'getForProducts']);
Route::post('/api/coupons/validate',     [CouponController::class, 'apply'])->name('coupons.validate');
Route::get('/api/cart/truck-check',      [ProductController::class, 'truckCheck']);

// ── Auth scaffolding ──────────────────────────────────────────────────────────
Auth::routes();
Route::get('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);
Route::get('/home', fn () => redirect('/'))->middleware('auth');
Route::get('/main', fn () => redirect('/'));


// ═══════════════════════════════════════════════════════════════════════════════
// AUTHENTICATED USER ROUTES
// ═══════════════════════════════════════════════════════════════════════════════


Route::get('/dashboard', fn () => redirect('/'));

Route::middleware('auth')->group(function () {

    // ── Email verification (used only when enabled in admin Settings) ──────────
    Route::get('/email/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'show'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\VerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');

    // ── Dashboard ─────────────────────────────────────────────────────────────
    // Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('/page-account-settings-account', [UserController::class, 'profile']);
    Route::get('/user/profile/edit',             [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile',                [UserProfileController::class, 'update'])->name('user.profile.update');

    // ── Account ───────────────────────────────────────────────────────────────
    Route::get('/account',          [AccountController::class, 'index'])->name('account.index');
    Route::patch('/account/update', [AccountController::class, 'update'])->name('account.update');

    Route::get('/account/change-password',  [AccountController::class, 'changePasswordForm'])->name('account.change-password');
    Route::post('/account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password.update');

    Route::get('/settings', fn () => view('settings'));

    // ── Profile completion / ID verification ──────────────────────────────────
    Route::get('await-success',     [UserController::class, 'await'])->name('account.await');
    Route::get('processing',        [UserController::class, 'processing'])->name('account.processing');
    Route::post('profile/complete', [UserController::class, 'completeProfile'])->name('account.profile.complete');
    Route::post('profile/verify',   [UserController::class, 'verifyID'])->name('await');

    Route::get('id-verification',  fn () => view('user.verification', ['user' => auth()->user()]));
    Route::get('kyc-verification', fn () => view('user.verification', ['user' => auth()->user()]));

    // ── Two-Factor Authentication ─────────────────────────────────────────────
    Route::get('/2fa',              [TwoFactorController::class, 'show'])->name('2fa');
    Route::post('/2fa',             [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/resend-2fa-code', [TwoFactorController::class, 'resend'])->name('resend-2fa');

    Route::get('/account/two-factor',          [TwoFactorSettingsController::class, 'show'])->name('two-factor.settings');
    Route::post('/account/two-factor/enable',  [TwoFactorSettingsController::class, 'enable'])->name('two-factor.enable');
    Route::post('/account/two-factor/disable', [TwoFactorSettingsController::class, 'disable'])->name('two-factor.disable');

    // ── Transactions ──────────────────────────────────────────────────────────
    Route::get('/transactions',     [UserTransactionController::class, 'index'])->name('user.transactions');
    Route::get('/api/transactions', [UserTransactionController::class, 'fetchTransactions']);

    // ── Orders ────────────────────────────────────────────────────────────────
    Route::get('/account/orders', [OrderController::class, 'index'])->name('account.orders');
    Route::get('/account/orders/{order}/invoice',          [OrderController::class, 'invoice'])->name('account.orders.invoice');
    
    Route::get('/account/orders/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('account.orders.invoice.download');
    Route::get('/account/orders/{order}/return',           [OrderController::class, 'showReturn'])->name('account.orders.return');
    Route::post('/account/orders/{order}/return',          [OrderController::class, 'submitReturn'])->name('account.orders.return.submit');
    Route::get('/account/orders/{order}/cancel',           [OrderController::class, 'showCancel'])->name('account.orders.cancel');
    Route::post('/account/orders/{order}/cancel',          [OrderController::class, 'submitCancel'])->name('account.orders.cancel.submit');

    Route::get('/orders', function () {
        $orders = Order::where('user_id', auth()->id())->with('items')->latest()->get();
        return view('sims/orders', compact('orders'));
    });

    // ── Checkout ──────────────────────────────────────────────────────────────
    // verified.optional gates these only when email verification is enabled in Settings.
    Route::get('/checkout',         [CartController::class, 'checkout'])->name('checkout.index')->middleware('verified.optional');
    Route::post('/checkout/submit', [CartController::class, 'submitCheckout'])->name('cart.checkout.submit')->middleware('verified.optional');

    // ── Payments ──────────────────────────────────────────────────────────────
    Route::post('/paystack/save-checkout', [OrderController::class, 'saveCheckout'])->name('paystack.save_checkout')->middleware(['throttle:checkout-save', 'verified.optional']);
    Route::post('/stripe/create-payment-intent', [PaymentController::class, 'createPaymentIntent'])->name('stripe.create_payment_intent')->middleware('verified.optional');
    Route::post('/paystack/initiate-payment',    [PaymentController::class, 'initiatePayment'])->name('paystack.initiate_payment')->middleware('verified.optional');
    Route::post('/verify-paystack-payment',      [PaymentController::class, 'verifyPaystackPayment'])->name('paystack.verify');
    Route::post('/initiate-payment',             [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/paystack/verify-payment',       [OrderController::class, 'verifyPaystack'])->name('paystack.verify_payment');
    Route::post('/orders/stripe',                [OrderController::class, 'storeStripeOrder']);
    // Removed: Route::post('/orders/paystack') — storePaystackOrder created orders from unverified client data (no Paystack API check). Use /paystack/confirm-order instead.

    // ── Chat ──────────────────────────────────────────────────────────────────
    Route::get('/chat',              [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{supplierId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send',        [ChatController::class, 'sendMessage'])->name('chat.send');

    // ── Support Tickets ───────────────────────────────────────────────────────
    Route::get('/tickets',                 [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create',          [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets',                [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}',        [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit',   [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}',        [TicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');

    // ── Reviews ───────────────────────────────────────────────────────────────
    Route::post('/products/{productId}/reviews',   [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/suppliers/{supplierId}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{reviewId}',              [ReviewController::class, 'update'])->name('reviews.update');

    // ── Suppliers (browsing) ──────────────────────────────────────────────────
    Route::get('/suppliers',                        [SupplierController::class, 'index'])->name('suppliers.list');
    Route::get('/supplier/{supplier}',              [SupplierProfileController::class, 'show'])->name('supplier.profile');
    Route::get('/supplier/{supplierId}/storefront', [SupplierProductController::class, 'showStorefront'])->name('supplier.products.storefront');
    Route::get('/supplier/products/{product}',      [SupplierProductController::class, 'show'])->name('supplier.products.show');

    // ── Distributors ──────────────────────────────────────────────────────────
    Route::get('/supplier/distributors',      [DistributorController::class, 'index'])->name('distributors.index');
    Route::get('/distributors/{distributor}', [DistributorController::class, 'show'])->name('distributors.show');

    // ── Affiliate ─────────────────────────────────────────────────────────────
    Route::prefix('affiliate')->name('affiliate.')->group(function () {
        Route::get('/dashboard',              [AffiliateController::class, 'dashboard'])->name('dashboard');
        Route::get('/generate-referral-link', [AffiliateController::class, 'generateReferralLink'])->name('generateReferralLink');
        Route::get('/earnings',               [AffiliateController::class, 'earnings'])->name('earnings');
    });

});


// ═══════════════════════════════════════════════════════════════════════════════
// SUPPLIER ROUTES
// ═══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {

    Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', SupplierProductController::class);
    Route::get('/transactions', [SupplierTransactionController::class, 'index'])->name('transactions.index');

    Route::get('/twofactor',                      [TwoFactorSettingsController::class, 'show']);
    Route::get('/page-account-settings-account',  fn () => view('user/profile'));
    Route::get('/page-account-settings-security', fn () => view('html/ltr/horizontal-menu-template/page-account-settings-security'));

});


// ═══════════════════════════════════════════════════════════════════════════════
// MANAGER ROUTES
// ═══════════════════════════════════════════════════════════════════════════════




// ═══════════════════════════════════════════════════════════════════════════════
// ADMIN ROUTES
// ═══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/paystack/refund-test-readiness', [\App\Http\Controllers\PaystackRefundStatusController::class, 'readiness'])->name('paystack.refund-test-readiness');
    Route::get('/orders/{order}/paystack-refund-status', [\App\Http\Controllers\PaystackRefundStatusController::class, 'show'])->middleware('throttle:10,1')->name('orders.paystack-refund-status');

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('/',          [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    Route::get('/data/user-analytics',        [AdminDashboardController::class, 'getUserAnalytics']);
    Route::get('/data/supplier-analytics',    [AdminDashboardController::class, 'getSupplierAnalytics']);
    Route::get('/data/transaction-analytics', [AdminDashboardController::class, 'getTransactionAnalytics']);
    Route::get('/data/product-analytics',     [AdminDashboardController::class, 'getProductAnalytics']);
    Route::get('/data/revenue-analytics',     [AdminDashboardController::class, 'getRevenueAnalytics']);
    Route::get('/data/order-status-analytics',[AdminDashboardController::class, 'getOrderStatusAnalytics']);

    // ── Users ─────────────────────────────────────────────────────────────────
    Route::get('users',             [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create',      [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users',            [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}',      [AdminUserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}',      [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}',   [AdminUserController::class, 'destroy'])->name('users.destroy');

        // ── Audit Trail ───────────────────────────────────────────────────────────
Route::get('audit', [AdminAuditController::class, 'index'])->name('audit.index');
// ── Shipping Settings ─────────────────────────────────────────────────────
Route::get('shipping', [AdminShippingController::class, 'index'])->name('shipping.index');
Route::put('shipping', [AdminShippingController::class, 'update'])->name('shipping.update');

// ── Store Settings ─────────────────────────────────────────────────────────
Route::get('settings',  [AdminSettingsController::class, 'index'])->name('settings.index');
Route::put('settings',  [AdminSettingsController::class, 'update'])->name('settings.update');

// ── Invoice Template ────────────────────────────────────────────────────────
Route::get('invoice-settings', [AdminInvoiceSettingsController::class, 'index'])->name('invoice.settings');
Route::put('invoice-settings', [AdminInvoiceSettingsController::class, 'update'])->name('invoice.settings.update');
Route::put('invoice-settings/layout', [AdminInvoiceSettingsController::class, 'updateLayout'])->name('invoice.settings.layout');

// ── Content Management ───────────────────────────────────────────────────────
Route::resource('faqs', AdminFaqController::class)->except(['show']);
Route::get('about',   [AdminAboutController::class,   'index'])->name('about.index');
Route::put('about',   [AdminAboutController::class,   'update'])->name('about.update');
Route::get('contact', [AdminContactController::class, 'index'])->name('contact.index');
Route::put('contact', [AdminContactController::class, 'update'])->name('contact.update');

    // ── Suppliers ─────────────────────────────────────────────────────────────
    Route::resource('suppliers', AdminSupplierController::class);
    Route::post('suppliers/{supplier}/verify', [AdminSupplierController::class, 'verify'])->name('suppliers.verify');

    // ── Products ──────────────────────────────────────────────────────────────
    Route::resource('products', AdminProductController::class);
    Route::post('products/bulk-markup',              [AdminProductController::class, 'bulkMarkup'])->name('products.bulkMarkup');
    Route::patch('products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggleActive');
    Route::delete('products/images/{imageId}',       [ProductController::class, 'removeImage'])->name('products.removeImage');

    // ── Categories & Subcategories ────────────────────────────────────────────
    Route::resource('categories', AdminCategoryController::class);
    Route::get('subcategories',                    [AdminCategoryController::class, 'SubcategoryIndex'])->name('subcategories.index');
    Route::get('subcategories/create',             [AdminCategoryController::class, 'createSubcategory'])->name('subcategories.create');
    Route::post('subcategories',                   [AdminCategoryController::class, 'storeSubcategory'])->name('subcategories.store');
    Route::get('subcategories/{Subcategory}/edit', [AdminCategoryController::class, 'editSubcategory'])->name('subcategories.edit');
    Route::put('subcategories/{Subcategory}',      [AdminCategoryController::class, 'updateSubcategory'])->name('subcategories.update');
    Route::delete('subcategories/{Subcategory}',   [AdminCategoryController::class, 'destroySubcategory'])->name('subcategories.destroy');

    // ── Brands ────────────────────────────────────────────────────────────────
    Route::get('brands/assign',  [BrandAssignmentController::class, 'index'])->name('brands.assign');
    Route::post('brands/assign', [BrandAssignmentController::class, 'assign'])->name('brands.assign.post');
    Route::resource('brands', AdminBrandController::class);

    // ── Orders ────────────────────────────────────────────────────────────────
    Route::get('orders/search-products', [AdminOrderController::class, 'searchProducts'])->name('orders.search-products');
    Route::get('orders/search-users',    [AdminOrderController::class, 'searchUsers'])->name('orders.search-users');
    Route::get('orders/{order}/invoice',          [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/invoice/download', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    Route::resource('orders', AdminOrderController::class);


    // ── Paystack Transactions ─────────────────────────────────────────────────
    Route::get('paystack-transactions', [AdminPaystackTransactionController::class, 'index'])->name('paystack-transactions.index');
    Route::get('paystack-transactions/{paystackTransaction}', [AdminPaystackTransactionController::class, 'show'])->name('paystack-transactions.show');

    // ── Returns & Cancellations ───────────────────────────────────────────────
    Route::get('orders-search',                        [AdminOrderController::class, 'searchOrders'])->name('orders.search');

    Route::get('returns',                              [AdminOrderController::class, 'returnsIndex'])->name('returns.index');
    Route::get('returns/create',                       [AdminOrderController::class, 'createReturn'])->name('returns.create');
    Route::post('returns',                             [AdminOrderController::class, 'storeReturnFromForm'])->name('returns.store');
    Route::post('orders/{order}/returns',              [AdminOrderController::class, 'storeReturn'])->name('orders.returns.store');
    Route::patch('returns/{return}/review',            [AdminOrderController::class, 'reviewReturn'])->name('returns.review');
    Route::get('cancellations',                        [AdminOrderController::class, 'cancellationsIndex'])->name('cancellations.index');
    Route::get('cancellations/create',                 [AdminOrderController::class, 'createCancellation'])->name('cancellations.create');
    Route::post('cancellations',                       [AdminOrderController::class, 'storeCancellationFromForm'])->name('cancellations.store');
    Route::post('orders/{order}/cancellations',        [AdminOrderController::class, 'storeCancellation'])->name('orders.cancellations.store');
    Route::patch('cancellations/{cancellation}/review',[AdminOrderController::class, 'reviewCancellation'])->name('cancellations.review');

    // ── Transactions ──────────────────────────────────────────────────────────
    Route::resource('transactions', AdminTransactionController::class);

    // ── Payment Methods ───────────────────────────────────────────────────────
    Route::resource('payment-methods', AdminPaymentMethodController::class);

    // ── Pickup Points ─────────────────────────────────────────────────────────
    Route::resource('pickup-points', AdminPickupPointController::class)->except(['show'])->names('pickup-points');

    // ── Store Locations ───────────────────────────────────────────────────────
    Route::resource('store-locations', AdminStoreLocationController::class)->except(['show'])->names('store-locations');

    // ── States ────────────────────────────────────────────────────────────────
    Route::resource('states', AdminStateController::class)->except(['show']);
    Route::patch('states/{state}/toggle-active', [AdminStateController::class, 'toggleActive'])->name('states.toggleActive');

    // ── Locations ─────────────────────────────────────────────────────────────
    Route::resource('locations', AdminLocationController::class);
    Route::patch('locations/{location}/toggle-active', [AdminLocationController::class, 'toggleActive'])->name('locations.toggleActive');

    // ── Cities ────────────────────────────────────────────────────────────────
    Route::resource('cities', AdminCityController::class);

    // ── Tags / Sizes / Colors ─────────────────────────────────────────────────
    Route::resource('tags',   AdminTagController::class);
    Route::resource('sizes',  AdminSizeController::class);
    Route::resource('colors', AdminColorController::class);

    // ── Banners ───────────────────────────────────────────────────────────────
    Route::get('banners',           [BannerController::class, 'index'])->name('banners.index');
    Route::get('banners/create',    [BannerController::class, 'create'])->name('banners.create');
    Route::post('banners/store',    [BannerController::class, 'store'])->name('banners.store');
    Route::get('banners/{type}',    [BannerController::class, 'show'])->name('banners.show');
    Route::get('banners/{id}/edit', [BannerController::class, 'edit'])->name('banners.edit');
    Route::put('banners/{id}',      [BannerController::class, 'update'])->name('banners.update');
    Route::delete('banners/{id}',   [BannerController::class, 'destroy'])->name('banners.destroy');

    // ── Reviews ───────────────────────────────────────────────────────────────
    Route::resource('reviews', AdminReviewController::class);

    // ── Support Tickets ───────────────────────────────────────────────────────
    Route::get('tickets',                 [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create',          [AdminTicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets',                [AdminTicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}',        [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::get('tickets/{ticket}/edit',   [AdminTicketController::class, 'edit'])->name('tickets.edit');
    Route::put('tickets/{ticket}',        [AdminTicketController::class, 'update'])->name('tickets.update');
    Route::post('tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');

    // ── Affiliates ────────────────────────────────────────────────────────────
    Route::get('affiliates',               [AdminAffiliateController::class, 'index'])->name('affiliates.index');
    Route::get('affiliates/create',        [AdminAffiliateController::class, 'create'])->name('affiliates.create');
    Route::get('affiliates/{id}',          [AdminAffiliateController::class, 'show'])->name('affiliates.show');
    Route::get('affiliates/{id}/edit',     [AdminAffiliateController::class, 'edit'])->name('affiliates.edit');
    Route::put('affiliates/{id}',          [AdminAffiliateController::class, 'update'])->name('affiliates.update');
    Route::put('affiliates/{id}/status',   [AdminAffiliateController::class, 'updateAffiliateStatus'])->name('affiliates.updateStatus');
    Route::get('affiliates/{id}/earnings', [AdminAffiliateController::class, 'earnings'])->name('affiliates.earnings');

    // ── Currencies ────────────────────────────────────────────────────────────
    Route::resource('currencies', CurrencyController::class)->except(['show']);

    // ── Weight manager ────────────────────────────────────────────────────────
    Route::prefix('weight')->name('weight.')->group(function () {
        Route::get('/',                                    [WeightController::class, 'index'])->name('index');
        Route::post('/thresholds',                         [WeightController::class, 'updateThresholds'])->name('thresholds.update');

        Route::post('/category/{category}',                       [WeightController::class, 'updateCategory'])->name('category.update');
        Route::delete('/category/{category}/clear',               [WeightController::class, 'clearCategory'])->name('category.clear');
        Route::delete('/category/{category}/clear-subcategories', [WeightController::class, 'clearCategorySubcategories'])->name('category.clearSubcategories');

        Route::post('/Subcategory/{Subcategory}',         [WeightController::class, 'updateSubcategory'])->name('Subcategory.update');
        Route::delete('/Subcategory/{Subcategory}/clear', [WeightController::class, 'clearSubcategory'])->name('Subcategory.clear');

        Route::post('/product/{product}',         [WeightController::class, 'updateProduct'])->name('product.update');
        Route::delete('/product/{product}/clear', [WeightController::class, 'clearProduct'])->name('product.clear');
    });

    // ── Markup ────────────────────────────────────────────────────────────────
    Route::prefix('markup')->name('markup.')->group(function () {
        Route::get('/', [MarkupController::class, 'index'])->name('index');

        Route::post('/category/{category}',                       [MarkupController::class, 'updateCategory'])->name('category.update');
        Route::delete('/category/{category}/clear',               [MarkupController::class, 'clearCategory'])->name('category.clear');
        Route::delete('/category/{category}/clear-subcategories', [MarkupController::class, 'clearCategorySubcategories'])->name('category.clearSubcategories');

        Route::post('/Subcategory/{Subcategory}',         [MarkupController::class, 'updateSubcategory'])->name('Subcategory.update');
        Route::delete('/Subcategory/{Subcategory}/clear', [MarkupController::class, 'clearSubcategory'])->name('Subcategory.clear');

        Route::post('/product/{product}',        [MarkupController::class, 'updateProduct'])->name('product.update');
        Route::delete('/product/{product}/clear', [MarkupController::class, 'clearProduct'])->name('product.clear');

        Route::post('/bulk-all', [MarkupController::class, 'bulkAll'])->name('bulk');
    });

    // ── Discounts ─────────────────────────────────────────────────────────────
    Route::prefix('discount')->name('discount.')->group(function () {
        Route::get('/', [DiscountController::class, 'index'])->name('index');

        Route::post('/category/{category}',                       [DiscountController::class, 'updateCategory'])->name('category.update');
        Route::delete('/category/{category}/clear',               [DiscountController::class, 'clearCategory'])->name('category.clear');
        Route::delete('/category/{category}/clear-subcategories', [DiscountController::class, 'clearCategorySubcategories'])->name('category.clearSubcategories');

        Route::post('/Subcategory/{Subcategory}',         [DiscountController::class, 'updateSubcategory'])->name('Subcategory.update');
        Route::delete('/Subcategory/{Subcategory}/clear', [DiscountController::class, 'clearSubcategory'])->name('Subcategory.clear');

        Route::post('/product/{product}',         [DiscountController::class, 'updateProduct'])->name('product.update');
        Route::delete('/product/{product}/clear', [DiscountController::class, 'clearProduct'])->name('product.clear');

        Route::post('/bulk-all',    [DiscountController::class, 'bulkAll'])->name('bulk');
        Route::delete('/clear-all', [DiscountController::class, 'clearAll'])->name('clearAll');
    });

    // ── Coupons ───────────────────────────────────────────────────────────────
    Route::resource('coupons', AdminCouponController::class)->except(['show']);
    Route::patch('coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])->name('coupons.toggleActive');

    // ── Email Tools ───────────────────────────────────────────────────────────
    Route::get('send-email',               [AdminController::class, 'showSendAdminEmailForm'])->name('send.email.form');
    Route::post('send-email',              [AdminController::class, 'sendAdminEmail'])->name('send.email');

});


// ═══════════════════════════════════════════════════════════════════════════════
// UTILITY / MISC
// ═══════════════════════════════════════════════════════════════════════════════

// Paystack webhook — no auth, no CSRF (excluded in VerifyCsrfToken::$except)
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handle'])
    ->name('webhooks.paystack')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/foo', function () {
    Artisan::call('storage:link');
});

Route::get('/cities', function (Request $request) {
    $countryCode = $request->query('country');
    return City::where('country_code', $countryCode)->get();
});

Route::get('/categories', function () {
    return response()->json(Category::with('subcategories')->get());
});

Route::post('/admin/upload-block-image', [AdminProductController::class, 'uploadBlockImage'])
    ->middleware('auth')
    ->name('admin.upload-block-image');

Route::post('/paystack/confirm-order', [OrderController::class, 'verifyAndStorePaystackOrder'])
    ->name('paystack.confirm_order')
    ->middleware(['auth', 'throttle:checkout-confirm']);

Route::patch('brands/{brand}/toggle-active', [AdminBrandController::class, 'toggleActive'])
    ->name('admin.brands.toggleActive');

Route::patch('categories/{category}/toggle-active', [AdminCategoryController::class, 'toggleActive'])
    ->name('admin.categories.toggleActive');

Route::patch('subcategories/{Subcategory}/toggle-active', [AdminCategoryController::class, 'toggleActiveSubcategory'])
    ->name('admin.subcategories.toggleActive');


Route::get('/admin/sync-brand-managers', function () {
    $linkedFixed   = 0;  // products with brand_id that gained a manager
    $legacyLinked  = 0;  // legacy string products linked to a Brand row
    $legacyFixed   = 0;  // legacy products that gained a manager via their brand string

    // ── Pass 1: products already linked by brand_id but missing a manager ──
    \App\Models\Product::whereNotNull('brand_id')
        ->whereNull('manager_id')
        ->chunkById(200, function ($products) use (&$linkedFixed) {
            foreach ($products as $product) {
                $managerId = \App\Models\Brand::whereKey($product->brand_id)->value('manager_id');
                if ($managerId) {
                    \Illuminate\Support\Facades\DB::table('products')
                        ->where('id', $product->id)
                        ->update(['manager_id' => $managerId]);
                    $linkedFixed++;
                }
            }
        });

    // ── Pass 2: legacy string-only products (no brand_id) ──────────────────
    // Match their brand string to a Brand row; link brand_id, and pull the
    // manager if the Brand has one.
    \App\Models\Product::whereNull('brand_id')
        ->whereNotNull('brand')
        ->where('brand', '!=', '')
        ->chunkById(200, function ($products) use (&$legacyLinked, &$legacyFixed) {
            foreach ($products as $product) {
                $brand = \App\Models\Brand::whereRaw('TRIM(name) = ?', [trim($product->brand)])->first();
                if (!$brand) {
                    continue; // no Brand row for this string yet — nothing to pull from
                }

                $payload = ['brand_id' => $brand->id];
                $legacyLinked++;

                if (!is_null($brand->manager_id)) {
                    $payload['manager_id'] = $brand->manager_id;
                    $legacyFixed++;
                }

                \Illuminate\Support\Facades\DB::table('products')
                    ->where('id', $product->id)
                    ->update($payload);
            }
        });

    return response()->json([
        'message'                     => 'Reconciliation complete.',
        'linked_products_fixed'       => $linkedFixed,
        'legacy_products_linked'      => $legacyLinked,
        'legacy_products_got_manager' => $legacyFixed,
    ]);
})->middleware(['auth', 'role:admin']);


Route::middleware(['auth', 'manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('manager.products.index'))->name('dashboard');
    Route::resource('products', ManagerProductController::class)->only(['index', 'edit', 'update']);
});

Route::middleware(['auth', 'manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('manager.products.index'))->name('dashboard');
    Route::patch('products/{product}/toggle-active', [ManagerProductController::class, 'toggleActive'])
        ->name('products.toggleActive');
    Route::resource('products', ManagerProductController::class)->only(['index', 'edit', 'update']);
});


Route::get('/guest/orders/{order}/invoice/download', [\App\Http\Controllers\OrderController::class, 'guestDownloadInvoice'])
    ->name('guest.orders.invoice.download')
    ->middleware('signed'); // This ensures only people with the exact email link can access it

Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    abort_unless(file_exists($file), 404);
    return response()->file($file);
})->where('path', '.*');

Route::get('/states', function () {
    return \App\Models\State::where('is_active', true)->orderBy('name')->get(['id', 'name']);
});

Route::get('/locations', function (\Illuminate\Http\Request $request) {
    $query = \App\Models\Location::where('is_active', true);
    if ($request->filled('state_id')) {
        $query->where('state_id', $request->query('state_id'));
    }
    return $query->orderBy('name')->get(['id', 'name', 'shipping_cost']);
});


// Add this alongside your existing location routes
Route::get('/states', [LocationController::class, 'states']);
