<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Render paginator ->links() with Bootstrap markup so it matches the
        // admin theme (the app otherwise uses hand-built Bootstrap pagers).
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            $topLevelCategories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['subcategories' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->get();
            $view->with('topLevelCategories', $topLevelCategories);

            $navBrands = Brand::where('is_active', true)
                ->select('name', 'slug')
                ->orderBy('name')
                ->take(14)
                ->get();
            $view->with('navBrands', $navBrands);

            // Share store settings globally (skip if the table doesn't exist yet)
            try {
                $view->with([
                    'appStoreName'     => Setting::get('store_name', config('app.name')),
                    'appStoreLogo'     => Setting::get('store_logo'),
                    'deliveryEnabled'  => Setting::get('delivery_enabled', '1') === '1',
                    'pickupEnabled'    => Setting::get('pickup_enabled', '1') === '1',
                    'storeAddress'     => Setting::get('store_address'),
                    'storePhone'       => Setting::get('store_phone'),
                    'storeEmail'       => Setting::get('store_email'),
                ]);
            } catch (\Throwable $e) {
                // Settings table not yet created — use defaults
                $view->with([
                    'appStoreName'    => config('app.name'),
                    'appStoreLogo'    => null,
                    'deliveryEnabled' => true,
                    'pickupEnabled'   => true,
                    'storeAddress'    => null,
                    'storePhone'      => null,
                    'storeEmail'      => null,
                ]);
            }
        });

        RateLimiter::for('contact-form', function (Request $request) {
            return [
                // Max 3 submissions per minute from the same IP
                Limit::perMinute(3)
                    ->by('contact-ip:' . $request->ip())
                    ->response(fn () => redirect()->back()->with(
                        'error',
                        "You're sending messages too quickly. Please wait a minute and try again."
                    )),

                // Max 5 submissions per day from the same email address
                Limit::perDay(5)
                    ->by('contact-email:' . strtolower($request->input('email', 'unknown')))
                    ->response(fn () => redirect()->back()->with(
                        'error',
                        "You've reached the daily limit for contact submissions. Please try again tomorrow."
                    )),
            ];
        });
    }
}