<?php

namespace App\Providers;
use App\Models\Cart;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
            $total_price = 0;

            if (Auth::check()) {
                $total_price = Cart::where('user_id', Auth::id())
                    ->selectRaw('SUM(price * quantity) as total')
                    ->value('total') ?? 0;
            }

            $view->with('total_price', $total_price);
        });
    }
}
