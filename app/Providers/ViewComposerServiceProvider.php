<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\HomeSetting;
use App\Models\Coupon;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $theme = HomeSetting::first();
            $userCoupons = collect();
            $promoCoupons = collect();
            try {
                if (auth()->check()) {
                    $userCoupons = Coupon::where('user_id', auth()->id())
                        ->where('status', true)
                        ->whereColumn('used_count', '<', 'usage_limit')
                        ->where(function ($q) {
                            $q->whereNull('expiration_date')->orWhere('expiration_date', '>', now());
                        })
                        ->orderBy('expiration_date')
                        ->orderByDesc('created_at')
                        ->get();
                }

                // Promo coupons logic
                $promoCoupons = Coupon::query()
                    ->whereNull('user_id')
                    ->where('status', true)
                    ->where(function ($q) {
                        $q->whereNull('expiration_date')->orWhere('expiration_date', '>', now());
                    })
                    ->when(
                        auth()->check(),
                        function ($q) {
                            $q->where('generated_for', 'manual');
                        },
                        function ($q) {
                            $q->whereIn('generated_for', ['manual', 'welcome_auto']);
                        },
                    )
                    ->orderBy('min_total')
                    ->orderByDesc('value')
                    ->take(4)
                    ->get()
                    ->filter(function ($c) {
                        if ($c->usage_limit && $c->usage_limit > 0) {
                            $delivered = Coupon::where('template_id', $c->id)->count();
                            return $delivered < $c->usage_limit;
                        }
                        // استبعاد إذا تجاوز المستخدم حد الاستخدام الشخصي
                        if (auth()->check() && $c->user_usage_limit && $c->user_usage_limit > 0) {
                            $usedCount = \App\Models\Checkout::where('user_id', auth()->id())
                                ->where('coupon_id', $c->id)
                                ->count();
                            if ($usedCount >= $c->user_usage_limit) {
                                return false;
                            }
                        }
                        return true;
                    });
            } catch (\Throwable $e) {
                $userCoupons = collect();
                $promoCoupons = collect();
            }
            $view->with('theme', $theme)
                ->with('userCoupons', $userCoupons)
                ->with('promoCoupons', $promoCoupons);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
