<?php
namespace App\Providers;

use App\Models\Shop;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            View::share('shop', Shop::firstOrFail());
        }

        \Illuminate\Support\Facades\Blade::directive('langL', function ($expression) {
            return "<?= transL({$expression}); ?>";
        });
    }
}
