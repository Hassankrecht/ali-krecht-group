<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminIncomeController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', 'last_30');
        $fromInput = $request->input('from');
        $toInput = $request->input('to');
        $from = $fromInput ? Carbon::parse($fromInput)->startOfDay() : null;
        $to = $toInput ? Carbon::parse($toInput)->endOfDay() : null;

        // إذا لم تُحدد تواريخ صريحة، نستخدم النطاق الجاهز
        if (!$from || !$to) {
            $now = Carbon::now();
            switch ($range) {
                case 'today':
                    $from = $now->copy()->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'last_7':
                    $from = $now->copy()->subDays(6)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'last_30':
                    $from = $now->copy()->subDays(29)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'last_90':
                    $from = $now->copy()->subDays(89)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'last_180':
                    $from = $now->copy()->subDays(179)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                case 'last_365':
                    $from = $now->copy()->subDays(364)->startOfDay();
                    $to = $now->copy()->endOfDay();
                    break;
                default:
                    $from = $now->copy()->subDays(29)->startOfDay();
                    $to = $now->copy()->endOfDay();
            }
        }

        // ignore payment method (single method cash)
        $method = null;
        $hasPaymentMethod = false;

        // **PAID ORDERS ONLY** - Filter for paid/completed orders only
        $baseQuery = DB::table('checkouts')
            ->whereBetween('created_at', [$from, $to])
            ->whereIn(DB::raw("LOWER(status)"), ['paid', 'completed']);

        $summary = (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue_after_discount')
            ->selectRaw('COALESCE(SUM(discount_amount),0) as discounts')
            ->selectRaw('COALESCE(SUM(total_before_discount),0) as gross_before_discount')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('COALESCE(AVG(total_price),0) as avg_order')
            ->first();

        // Cards: today/month/year (respecting filters for status/method)
        $today = (clone $baseQuery)
            ->whereDate('created_at', Carbon::today())
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')->selectRaw('COUNT(*) as orders')->first();
        $thisMonth = (clone $baseQuery)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')->selectRaw('COUNT(*) as orders')->first();
        $thisYear = (clone $baseQuery)
            ->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')->selectRaw('COUNT(*) as orders')->first();
        $totals = (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')->selectRaw('COUNT(*) as orders')->first();

        // For paid orders, no need to group by status since we only show paid orders
        // Remove byStatus calculation

        // Daily breakdown: revenue and orders with vs without discounts
        $chartBreakdown = (clone $baseQuery)
            ->selectRaw('date(created_at) as day')
            ->selectRaw("COALESCE(SUM(CASE WHEN coupon_id IS NOT NULL THEN total_price ELSE 0 END),0) as revenue_with_disc")
            ->selectRaw("COALESCE(SUM(CASE WHEN coupon_id IS NULL THEN total_price ELSE 0 END),0) as revenue_without_disc")
            ->selectRaw("SUM(CASE WHEN coupon_id IS NOT NULL THEN 1 ELSE 0 END) as orders_with_disc")
            ->selectRaw("SUM(CASE WHEN coupon_id IS NULL THEN 1 ELSE 0 END) as orders_without_disc")
            ->groupBy(DB::raw('date(created_at)'))
            ->orderBy('day')
            ->get();

        $chartLabels = $chartBreakdown->pluck('day');
        $chartRevenueWithDiscount = $chartBreakdown->pluck('revenue_with_disc');
        $chartRevenueWithoutDiscount = $chartBreakdown->pluck('revenue_without_disc');
        $chartOrdersWithDiscount = $chartBreakdown->pluck('orders_with_disc');
        $chartOrdersWithoutDiscount = $chartBreakdown->pluck('orders_without_disc');
        $chartRevenue = $chartRevenueWithDiscount->zip($chartRevenueWithoutDiscount)
            ->map(fn ($pair) => (float) ($pair[0] + $pair[1]));
        $chartOrders = $chartOrdersWithDiscount->zip($chartOrdersWithoutDiscount)
            ->map(fn ($pair) => (int) ($pair[0] + $pair[1]));

        $recent = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $ordersQuery = (clone $baseQuery)->orderByDesc('created_at');
        $orders = (clone $ordersQuery)
            ->paginate(20)
            ->withQueryString();
        $rawOrders = $ordersQuery->get();
        $subtotalAll = $ordersQuery->sum(DB::raw('COALESCE(total_before_discount, total_price + discount_amount)'));

        // Analytics: gross/refund/net and averages
        $daysInRange = max(1, $from->diffInDays($to) + 1);
        // Gross = total_price + discount (قبل الخصم) بافتراض total_price بعد الخصم
        // sums
        $sumPrice = (clone $baseQuery)->sum('total_price');
        $sumDiscount = (clone $baseQuery)->sum('discount_amount');
        $sumBefore = (clone $baseQuery)->sum('total_before_discount');
        $hasPaidAt = Schema::hasColumn('checkouts', 'paid_at');

        // **PAID ORDERS - Gross Revenue** = Sum before discount
        $gross = (clone $baseQuery)->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN total_before_discount IS NOT NULL AND total_before_discount > 0 THEN total_before_discount
                    ELSE total_price + discount_amount
                END
            ),0) as gross_calc
        ")->value('gross_calc');

        // **PAID ORDERS - Net Revenue** = Sum after discount (total_price)
        $net = (clone $baseQuery)->sum('total_price');

        // **Total Discounts** (on paid orders)
        $discounts = (clone $baseQuery)->sum('discount_amount');

        // **Refunds** (on paid orders only)
        $refunds = (clone $baseQuery)
            ->whereIn(DB::raw("LOWER(status)"), ['refunded'])
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN refund_amount IS NOT NULL AND refund_amount > 0 THEN refund_amount
                        ELSE total_price
                    END
                ),0) as refund_sum
            ")->value('refund_sum');

        // **Gross Revenue with Discount** = Sum of paid orders WITH coupons (before discount)
        $grossWithDiscount = (clone $baseQuery)
            ->whereNotNull('coupon_id')
            ->selectRaw("
                COALESCE(SUM(
                    CASE
                        WHEN total_before_discount IS NOT NULL AND total_before_discount > 0 THEN total_before_discount
                        ELSE total_price + discount_amount
                    END
                ),0) as gross_calc
            ")->value('gross_calc');

        // **Net Revenue with Discount** = Sum of paid orders WITH coupons (after discount)
        $netWithDiscount = (clone $baseQuery)->whereNotNull('coupon_id')->sum('total_price');
        $ordersWithDiscount = (clone $baseQuery)->whereNotNull('coupon_id')->count();

        // **Revenue without Discount** = Sum of paid orders WITHOUT coupons
        $revenueWithoutDiscount = (clone $baseQuery)->whereNull('coupon_id')->sum('total_price');
        $ordersWithoutDiscount = (clone $baseQuery)->whereNull('coupon_id')->count();

        // **Orders Paid Count**
        $ordersCount = (clone $baseQuery)->count();

        // **Metrics**
        $avgOrderValue = $ordersCount > 0 ? $net / $ordersCount : 0;
        $avgDailyRevenue = $net / $daysInRange;
        $avgDailyOrders = $ordersCount / $daysInRange;

        // Growth vs previous same length (for paid orders)
        $prevFrom = (clone $from)->subDays($daysInRange);
        $prevTo = (clone $from)->subDay();
        $prevQuery = DB::table('checkouts')
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->whereIn(DB::raw("LOWER(status)"), ['paid', 'completed']);
        $prevSummary = (clone $prevQuery)
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')
            ->selectRaw('COUNT(*) as orders')
            ->first();

        // Fallback: if no SQL grouping, do it in PHP:
        if ($chartLabels->isEmpty()) {
            $rawOrders = (clone $baseQuery)->get();
            $grouped = [];
            foreach ($rawOrders as $o) {
                $day = Carbon::parse($o->created_at)->format('Y-m-d');
                if (!isset($grouped[$day])) {
                    $grouped[$day] = [
                        'revenue_with_disc' => 0,
                        'revenue_without_disc' => 0,
                        'orders_with_disc' => 0,
                        'orders_without_disc' => 0,
                    ];
                }
                if ($o->coupon_id) {
                    $grouped[$day]['revenue_with_disc'] += (float) $o->total_price;
                    $grouped[$day]['orders_with_disc'] += 1;
                } else {
                    $grouped[$day]['revenue_without_disc'] += (float) $o->total_price;
                    $grouped[$day]['orders_without_disc'] += 1;
                }
            }
            ksort($grouped);
            $chartLabels = collect(array_keys($grouped));
            $chartRevenueWithDiscount = collect(array_column($grouped, 'revenue_with_disc'));
            $chartRevenueWithoutDiscount = collect(array_column($grouped, 'revenue_without_disc'));
            $chartOrdersWithDiscount = collect(array_column($grouped, 'orders_with_disc'));
            $chartOrdersWithoutDiscount = collect(array_column($grouped, 'orders_without_disc'));
            $chartRevenue = $chartRevenueWithDiscount->zip($chartRevenueWithoutDiscount)
                ->map(fn ($pair) => (float) ($pair[0] + $pair[1]));
            $chartOrders = $chartOrdersWithDiscount->zip($chartOrdersWithoutDiscount)
                ->map(fn ($pair) => (int) ($pair[0] + $pair[1]));
        }

        $growthRevenue = $prevSummary->revenue > 0
            ? (($net - $prevSummary->revenue) / $prevSummary->revenue) * 100
            : null;
        $growthOrders = $prevSummary->orders > 0
            ? (($ordersCount - $prevSummary->orders) / $prevSummary->orders) * 100
            : null;

        $paymentPie = collect();

        return view('admins.reports.income', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'range' => $range,
            'summary' => $summary,
            'discounts' => $discounts,
            'gross' => $gross,
            'net' => $net,
            'refunds' => $refunds,
            'grossWithDiscount' => $grossWithDiscount,
            'netWithDiscount' => $netWithDiscount,
            'ordersWithDiscount' => $ordersWithDiscount,
            'revenueWithoutDiscount' => $revenueWithoutDiscount,
            'ordersWithoutDiscount' => $ordersWithoutDiscount,
            'ordersCount' => $ordersCount,
            'avgOrderValue' => $avgOrderValue,
            'avgDailyRevenue' => $avgDailyRevenue,
            'avgDailyOrders' => $avgDailyOrders,
            'growthRevenue' => $growthRevenue,
            'growthOrders' => $growthOrders,
            'chartLabels' => $chartLabels,
            'chartRevenue' => $chartRevenue,
            'chartRevenueWithDiscount' => $chartRevenueWithDiscount,
            'chartRevenueWithoutDiscount' => $chartRevenueWithoutDiscount,
            'chartOrders' => $chartOrders,
            'chartOrdersWithDiscount' => $chartOrdersWithDiscount,
            'chartOrdersWithoutDiscount' => $chartOrdersWithoutDiscount,
            'recent' => $recent,
            'orders' => $orders,
            'subtotalAll' => $subtotalAll,
        ]);
    }

    public function export(Request $request)
    {
        $range = $request->input('range', 'last_30');
        $from = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to = $request->input('to') ? Carbon::parse($request->input('to'))->endOfDay() : null;
        $hasPaymentMethod = Schema::hasColumn('checkouts', 'payment_method');
        if (!$from || !$to) {
            $now = Carbon::now();
            $from = $now->copy()->subDays(29)->startOfDay();
            $to = $now->copy()->endOfDay();
        }
        $status = $request->input('status');
        $method = $request->input('payment_method');

        $query = DB::table('checkouts')
            ->whereBetween('created_at', [$from, $to])
            ->whereIn(DB::raw("LOWER(status)"), ['paid', 'completed']);

        $rows = $query->orderByDesc('created_at')->get();

        $filename = 'income_export_' . now()->format('Ymd_His') . '.csv';
        $callback = function () use ($rows, $hasPaymentMethod) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Status', 'Payment Method', 'Subtotal', 'Discount', 'Total (after)', 'Date']);
            foreach ($rows as $r) {
                $subtotal = $r->total_before_discount ?? ($r->total_price + $r->discount_amount);
                fputcsv($out, [
                    $r->id,
                    $r->status,
                    $hasPaymentMethod ? $r->payment_method : '',
                    $subtotal,
                    $r->discount_amount,
                    $r->total_price,
                    $r->created_at,
                ]);
            }
            fclose($out);
        };
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
