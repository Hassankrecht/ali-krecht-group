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

        $status = $request->input('status'); // payment status
        // ignore payment method (single method cash)
        $method = null;
        // treat "null" string as no filter
        if ($status === 'null') {
            $status = null;
        }
        $hasPaymentMethod = false;

        // استخدم الفاصل الزمني الكامل لتفادي مشاكل التقريب إلى التاريخ فقط
        $baseQuery = DB::table('checkouts')
            ->whereBetween('created_at', [$from, $to]);

        if ($status) {
            $baseQuery->where('status', $status);
        }
        // payment method not used

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

        $byStatus = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as orders'), DB::raw('COALESCE(SUM(total_price),0) as revenue'))
            ->groupBy('status')
            ->get();

        $chartData = (clone $baseQuery)
            ->selectRaw('date(created_at) as day')
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')
            ->groupBy(DB::raw('date(created_at)'))
            ->orderBy('day')
            ->get();

        $chartOrders = (clone $baseQuery)
            ->selectRaw('date(created_at) as day')
            ->selectRaw('COUNT(*) as orders')
            ->groupBy(DB::raw('date(created_at)'))
            ->orderBy('day')
            ->get();

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

        // Gross: مجموع قبل الخصم إن وُجد لكل صف، وإلا (total_price + discount_amount) للصف
        $gross = (clone $baseQuery)->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN total_before_discount IS NOT NULL AND total_before_discount > 0 THEN total_before_discount
                    ELSE total_price + discount_amount
                END
            ),0) as gross_calc
        ")->value('gross_calc');

        // Refunds: فقط للطلبات المدفوعة والمُلغاة/المُعادة
        $refundQuery = (clone $baseQuery)
            ->whereIn(DB::raw("LOWER(status)"), ['cancelled', 'canceled', 'refunded']);
        if ($hasPaidAt) {
            $refundQuery->whereNotNull('paid_at');
        }
        $refunds = $refundQuery->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN refund_amount IS NOT NULL AND refund_amount > 0 THEN refund_amount
                    ELSE total_price
                END
            ),0) as refund_sum
        ")->value('refund_sum');

        $discounts = $sumDiscount;
        $net = $gross - $discounts - $refunds;
        $avgOrderValue = $summary->orders_count > 0 ? $net / $summary->orders_count : 0;
        $avgDailyRevenue = $net / $daysInRange;
        $avgDailyOrders = $summary->orders_count / $daysInRange;

        // Revenue with/without coupon
        $revenueWithCoupon = (clone $baseQuery)->whereNotNull('coupon_id')->sum('total_price');
        $revenueWithoutCoupon = (clone $baseQuery)->whereNull('coupon_id')->sum('total_price');
        $ordersWithCoupon = (clone $baseQuery)->whereNotNull('coupon_id')->count();
        $ordersWithoutCoupon = (clone $baseQuery)->whereNull('coupon_id')->count();

        // Growth vs previous same length
        $prevFrom = (clone $from)->subDays($daysInRange);
        $prevTo = (clone $from)->subDay();
        $prevQuery = DB::table('checkouts')
            ->whereBetween('created_at', [$prevFrom, $prevTo]);
        if ($status) $prevQuery->where('status', $status);
        if ($method && $hasPaymentMethod) $prevQuery->where('payment_method', $method);
        $prevSummary = (clone $prevQuery)
            ->selectRaw('COALESCE(SUM(total_price),0) as revenue')
            ->selectRaw('COUNT(*) as orders')
            ->first();
        $growthRevenue = $prevSummary->revenue > 0
            ? (($gross - $prevSummary->revenue) / $prevSummary->revenue) * 100
            : null;
        $growthOrders = $prevSummary->orders > 0
            ? (($summary->orders_count - $prevSummary->orders) / $prevSummary->orders) * 100
            : null;

        // في حال لم تُرجع الاستعلامات بيانات (أو عمود غير موجود)، استخدم تجميع PHP كاحتياط
        if ($chartData->isEmpty() && $rawOrders->count()) {
            $phpGroup = $rawOrders->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->toDateString();
            })->map(function ($items, $day) {
                return [
                    'day' => $day,
                    'revenue' => $items->sum('total_price'),
                    'orders' => $items->count(),
                ];
            })->values();
            $chartData = $phpGroup->map(fn($row) => (object)['day' => $row['day'], 'revenue' => $row['revenue']]);
            $chartOrders = $phpGroup->map(fn($row) => (object)['day' => $row['day'], 'orders' => $row['orders']]);
        }

        // Align orders to revenue labels
        $chartLabels = $chartData->pluck('day');
        $ordersMap = $chartOrders->pluck('orders', 'day');
        $ordersSeries = $chartLabels->map(function ($day) use ($ordersMap) {
            return (int) ($ordersMap[$day] ?? 0);
        });

        // Orders with/without coupon per day
        $ordersWithCouponByDay = (clone $baseQuery)
            ->whereNotNull('coupon_id')
            ->selectRaw('date(created_at) as day')
            ->selectRaw('COUNT(*) as orders')
            ->groupBy(DB::raw('date(created_at)'))
            ->pluck('orders', 'day');
        $ordersWithoutCouponByDay = (clone $baseQuery)
            ->whereNull('coupon_id')
            ->selectRaw('date(created_at) as day')
            ->selectRaw('COUNT(*) as orders')
            ->groupBy(DB::raw('date(created_at)'))
            ->pluck('orders', 'day');
        $ordersWithCouponSeries = $chartLabels->map(fn($d) => (int) ($ordersWithCouponByDay[$d] ?? 0));
        $ordersWithoutCouponSeries = $chartLabels->map(fn($d) => (int) ($ordersWithoutCouponByDay[$d] ?? 0));

        $paymentPie = collect();

        $statuses = DB::table('checkouts')->distinct()->pluck('status')->filter()->values();
        $methods = collect();

        return view('admins.reports.income', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'status' => $status,
            'paymentMethod' => $method,
            'hasPaymentMethod' => $hasPaymentMethod,
            'range' => $range,
            'summary' => $summary,
            'sumPrice' => $sumPrice,
            'discounts' => $discounts,
            'gross' => $gross,
            'refunds' => $refunds,
            'net' => $net,
            'avgOrderValue' => $avgOrderValue,
            'avgDailyRevenue' => $avgDailyRevenue,
            'avgDailyOrders' => $avgDailyOrders,
            'revenueWithCoupon' => $revenueWithCoupon,
            'revenueWithoutCoupon' => $revenueWithoutCoupon,
            'ordersWithCoupon' => $ordersWithCoupon,
            'ordersWithoutCoupon' => $ordersWithoutCoupon,
            'growthRevenue' => $growthRevenue,
            'growthOrders' => $growthOrders,
            'byStatus' => $byStatus,
            'chartLabels' => $chartLabels,
            'chartRevenue' => $chartData->pluck('revenue'),
            'chartOrdersSeries' => $ordersSeries,
            'chartOrdersWithCoupon' => $ordersWithCouponSeries,
            'chartOrdersWithoutCoupon' => $ordersWithoutCouponSeries,
            'paymentPie' => $paymentPie,
            'recent' => $recent,
            'orders' => $orders,
            'subtotalAll' => $subtotalAll,
            'statuses' => $statuses,
            'methods' => $methods,
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
            ->whereBetween('created_at', [$from, $to]);
        if ($status) $query->where('status', $status);
        if ($method && $hasPaymentMethod) $query->where('payment_method', $method);

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
