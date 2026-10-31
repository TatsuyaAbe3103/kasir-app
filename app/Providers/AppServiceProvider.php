<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $view->with('appBrandName', Setting::get('app_brand_name', 'NUSAPOS'));
                    $view->with('appBrandTagline', Setting::get('app_brand_tagline', 'Cafe & Resto System'));
                    $view->with('appBrandLogo', Setting::get('app_brand_logo', null));
                }
            } catch (\Exception $e) {
                // Fallback if DB not ready
                $view->with('appBrandName', 'NUSAPOS');
                $view->with('appBrandTagline', 'Cafe & Resto System');
                $view->with('appBrandLogo', null);
            }
        });
    }
}
