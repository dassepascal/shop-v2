<?php
namespace App\Providers;

use App\Models\Shop;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Menu;

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

        View::composer(['components.layouts.app' ], function ($view) {
            $view->with(
                'menus',
                Menu::with(['submenus' => function ($query) {
                    $query->orderBy('order');
                }])->orderBy('order')->get()
            );
        });
    }
}
