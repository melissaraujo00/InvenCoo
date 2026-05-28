<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Netflie\WhatsAppCloudApi\WebHook\Notification\Support\Products;

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
        Products::observe(ProductObserver::class);
        
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
