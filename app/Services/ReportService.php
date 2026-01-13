<?php

namespace App\Services;

use App\Models\Checkout;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Get sales report for date range
     */
    public function salesReport(Carbon $from, Carbon $to, string $groupBy = 'day'): Collection
    {
        return Checkout::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('DATE(' . ($groupBy === 'month' ? 'MONTH' : 'DATE') . '(created_at)) as date')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(total) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get revenue statistics
     */
    public function revenueStats(Carbon $from, Carbon $to): array
    {
        $query = Checkout::whereBetween('created_at', [$from, $to])->where('status', 'completed');

        return [
            'total_revenue' => $query->sum('total'),
            'average_order_value' => $query->avg('total'),
            'total_orders' => $query->count(),
            'total_items_sold' => $query->sum('items_count') ?? 0,
        ];
    }

    /**
     * Get top selling products
     */
    public function topProducts(int $limit = 10, Carbon $from = null, Carbon $to = null): Collection
    {
        $query = Product::query();

        if ($from && $to) {
            $query->whereHas('checkoutItems', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            });
        }

        return $query->withCount('checkoutItems')
            ->orderByDesc('checkout_items_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get customer analytics
     */
    public function customerAnalytics(Carbon $from, Carbon $to): array
    {
        $checkouts = Checkout::whereBetween('created_at', [$from, $to])->get();

        return [
            'total_customers' => $checkouts->pluck('user_id')->unique()->count(),
            'new_customers' => $checkouts->filter(fn($c) => $c->user->created_at >= $from)->count(),
            'repeat_customers' => $checkouts->pluck('user_id')
                ->countBy()
                ->filter(fn($count) => $count > 1)
                ->count(),
            'average_customer_value' => $checkouts->avg('total'),
        ];
    }

    /**
     * Get category performance
     */
    public function categoryPerformance(Carbon $from = null, Carbon $to = null): Collection
    {
        $query = \App\Models\Category::query();

        if ($from && $to) {
            $query->whereHas('products.checkoutItems', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            });
        }

        return $query->withCount('products')
            ->with(['products' => function ($q) {
                $q->withCount('checkoutItems');
            }])
            ->get();
    }

    /**
     * Get product reviews summary
     */
    public function reviewsSummary(Carbon $from, Carbon $to): array
    {
        $reviews = Review::whereBetween('created_at', [$from, $to])->get();

        return [
            'total_reviews' => $reviews->count(),
            'approved_reviews' => $reviews->where('is_approved', true)->count(),
            'pending_reviews' => $reviews->where('is_approved', false)->count(),
            'average_rating' => $reviews->avg('rating'),
            'ratings_distribution' => $reviews->groupBy('rating')
                ->map(fn($group) => $group->count()),
        ];
    }

    /**
     * Get status breakdown
     */
    public function statusBreakdown(Carbon $from, Carbon $to): array
    {
        return Checkout::whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->selectRaw('status')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as total')
            ->pluck('total', 'status')
            ->toArray();
    }

    /**
     * Generate PDF report
     */
    public function generatePdf(array $data, string $filename = 'report.pdf')
    {
        $html = view('reports.template', $data)->render();
        return Pdf::loadHTML($html)->download($filename);
    }

    /**
     * Get inventory report
     */
    public function inventoryReport(): Collection
    {
        return Product::selectRaw('
            id,
            title,
            stock,
            CASE 
                WHEN stock = 0 THEN "Out of Stock"
                WHEN stock < 10 THEN "Low Stock"
                ELSE "In Stock"
            END as status
        ')
        ->orderBy('stock')
        ->get();
    }

    /**
     * Get monthly comparison
     */
    public function monthlyComparison(int $months = 12): Collection
    {
        $from = now()->subMonths($months);
        
        return Checkout::where('created_at', '>=', $from)
            ->where('status', 'completed')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month')
            ->selectRaw('COUNT(*) as orders')
            ->selectRaw('SUM(total) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Get payment method distribution
     */
    public function paymentMethods(Carbon $from = null, Carbon $to = null): Collection
    {
        $query = Checkout::query();

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->groupBy('payment_method')
            ->selectRaw('payment_method')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as total')
            ->get();
    }
}
