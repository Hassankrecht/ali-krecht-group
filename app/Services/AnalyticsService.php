<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Checkout;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    /**
     * Track page view
     */
    public function pageView(string $page, ?User $user = null): void
    {
        $key = "page_views:$page";
        Cache::increment($key);
        Cache::forget($key, 86400); // 24 hours

        if ($user) {
            $userKey = "user_views:{$user->id}:$page";
            Cache::increment($userKey);
        }
    }

    /**
     * Track product view
     */
    public function trackProductView(Product $product, ?User $user = null): void
    {
        $product->increment('views');
        $this->pageView("product:{$product->id}", $user);
    }

    /**
     * Track search query
     */
    public function trackSearch(string $query, int $results = 0): void
    {
        Cache::increment("search:$query");
        Cache::increment("search:total");

        if ($results === 0) {
            Cache::increment("search:no_results:$query");
        }
    }

    /**
     * Track user action
     */
    public function trackAction(User $user, string $action, ?string $target = null): void
    {
        $key = "action:{$user->id}:$action";
        Cache::increment($key);

        if ($target) {
            $key .= ":$target";
            Cache::increment($key);
        }
    }

    /**
     * Track conversion
     */
    public function trackConversion(User $user, float $value): void
    {
        Cache::increment("conversions:total");
        Cache::increment("conversions:value", $value);
        
        $userKey = "conversions:user:{$user->id}";
        Cache::increment($userKey);
    }

    /**
     * Get popular products
     */
    public function popularProducts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Product::orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    /**
     * Get trending searches
     */
    public function trendingSearches(int $limit = 10): array
    {
        return Cache::get('search:queries', []);
    }

    /**
     * Get user analytics
     */
    public function userAnalytics(User $user): array
    {
        return [
            'total_visits' => Cache::get("user_views:{$user->id}", 0),
            'total_purchases' => $user->checkouts()->count(),
            'total_spent' => $user->checkouts()->sum('total'),
            'average_order_value' => $user->checkouts()->avg('total') ?? 0,
            'last_purchase' => $user->checkouts()->latest()->first()?->created_at,
        ];
    }

    /**
     * Get conversion rate
     */
    public function conversionRate(): float
    {
        $visitors = Cache::get('page_views:/', 0);
        $conversions = Cache::get('conversions:total', 0);

        if ($visitors === 0) {
            return 0;
        }

        return ($conversions / $visitors) * 100;
    }

    /**
     * Get funnel analytics
     */
    public function funnelAnalytics(): array
    {
        $views = Cache::get('page_views:/', 0);
        $cartAdds = Cache::get('page_views:/cart', 0);
        $checkouts = Checkout::count();

        return [
            'landing_page_views' => $views,
            'cart_additions' => $cartAdds,
            'completed_orders' => $checkouts,
            'view_to_cart_rate' => $views > 0 ? ($cartAdds / $views) * 100 : 0,
            'cart_to_checkout_rate' => $cartAdds > 0 ? ($checkouts / $cartAdds) * 100 : 0,
            'overall_conversion_rate' => $this->conversionRate(),
        ];
    }

    /**
     * Get user cohort analysis
     */
    public function cohortAnalysis(string $period = 'month'): array
    {
        $users = User::all();
        $cohorts = [];

        foreach ($users as $user) {
            $cohortKey = match ($period) {
                'week' => $user->created_at->weekOfYear,
                'month' => $user->created_at->format('Y-m'),
                'year' => $user->created_at->format('Y'),
                default => $user->created_at->format('Y-m'),
            };

            if (!isset($cohorts[$cohortKey])) {
                $cohorts[$cohortKey] = ['created' => 0, 'active' => 0];
            }

            $cohorts[$cohortKey]['created']++;
            
            if ($user->checkouts()->count() > 0) {
                $cohorts[$cohortKey]['active']++;
            }
        }

        return $cohorts;
    }

    /**
     * Get retention metrics
     */
    public function retentionMetrics(): array
    {
        $totalUsers = User::count();
        $returningUsers = User::whereHas('checkouts', function ($q) {
            $q->count();
        })->count();

        return [
            'total_users' => $totalUsers,
            'returning_users' => $returningUsers,
            'retention_rate' => $totalUsers > 0 ? ($returningUsers / $totalUsers) * 100 : 0,
            'churn_rate' => $totalUsers > 0 ? (($totalUsers - $returningUsers) / $totalUsers) * 100 : 0,
        ];
    }

    /**
     * Get revenue analytics
     */
    public function revenueAnalytics(int $days = 30): array
    {
        $from = now()->subDays($days);
        $checkouts = Checkout::where('created_at', '>=', $from)->get();

        return [
            'total_revenue' => $checkouts->sum('total'),
            'average_revenue_per_day' => $checkouts->count() > 0 
                ? $checkouts->sum('total') / $days 
                : 0,
            'total_orders' => $checkouts->count(),
            'average_order_value' => $checkouts->avg('total') ?? 0,
            'top_payment_method' => $checkouts->groupBy('payment_method')
                ->map->count()
                ->sortDesc()
                ->first(),
        ];
    }

    /**
     * Get device analytics
     */
    public function deviceAnalytics(): array
    {
        // This would typically use browser/device detection library
        return [
            'desktop' => Cache::get('device:desktop', 0),
            'mobile' => Cache::get('device:mobile', 0),
            'tablet' => Cache::get('device:tablet', 0),
        ];
    }

    /**
     * Get geographic analytics
     */
    public function geographicAnalytics(): array
    {
        return Checkout::groupBy('country')
            ->selectRaw('country')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as revenue')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Export analytics data
     */
    public function exportData(string $format = 'csv'): string
    {
        $data = [
            'users' => User::count(),
            'products' => Product::count(),
            'orders' => Checkout::count(),
            'revenue' => Checkout::sum('total'),
        ];

        return match ($format) {
            'json' => json_encode($data),
            'csv' => $this->toCsv($data),
            default => json_encode($data),
        };
    }

    /**
     * Convert array to CSV
     */
    private function toCsv(array $data): string
    {
        $csv = "metric,value\n";
        foreach ($data as $key => $value) {
            $csv .= "$key,$value\n";
        }
        return $csv;
    }
}
