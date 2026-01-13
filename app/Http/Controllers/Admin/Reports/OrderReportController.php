<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderReportController extends Controller
{
    private function baseQuery(Request $request)
    {
        $range = $request->input('range');
        $from = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to = $request->input('to') ? Carbon::parse($request->input('to'))->endOfDay() : null;

        if ($range && (!$from || !$to)) {
            $now = Carbon::now();
            switch ($range) {
                case 'daily':
                    $from = $now->copy()->startOfDay();
                    $to   = $now->copy()->endOfDay();
                    break;
                case 'weekly':
                    $from = $now->copy()->subDays(6)->startOfDay();
                    $to   = $now->copy()->endOfDay();
                    break;
                case 'monthly':
                    $from = $now->copy()->subDays(29)->startOfDay();
                    $to   = $now->copy()->endOfDay();
                    break;
                case '3m':
                    $from = $now->copy()->subMonthsNoOverflow(3)->startOfDay();
                    $to   = $now->copy()->endOfDay();
                    break;
                case '6m':
                    $from = $now->copy()->subMonthsNoOverflow(6)->startOfDay();
                    $to   = $now->copy()->endOfDay();
                    break;
                case '1y':
                    $from = $now->copy()->subYear()->startOfDay();
                    $to   = $now->copy()->endOfDay();
                    break;
            }
        }

        // fallback defaults
        $from = $from ?: Carbon::now()->subDays(29)->startOfDay();
        $to   = $to   ?: Carbon::now()->endOfDay();

        $q = DB::table('checkouts')
            ->leftJoin('users', 'checkouts.user_id', '=', 'users.id')
            ->leftJoin('coupons', 'checkouts.coupon_id', '=', 'coupons.id')
            ->whereBetween('checkouts.created_at', [$from, $to]);

        if ($status = $request->input('status')) {
            $q->where('checkouts.status', $status);
        }

        // Discount filter: with / without / all
        $discountFilter = $request->input('discount_filter');
        if ($discountFilter === 'with') {
            $q->where(function ($sub) {
                $sub->whereNotNull('checkouts.coupon_id')
                    ->orWhere('checkouts.discount_amount', '>', 0);
            });
        } elseif ($discountFilter === 'without') {
            $q->whereNull('checkouts.coupon_id')
                ->where(function ($sub) {
                    $sub->whereNull('checkouts.discount_amount')->orWhere('checkouts.discount_amount', '=', 0);
                });
        }

        if ($request->filled('min_total')) {
            $q->where('checkouts.total_price', '>=', (float) $request->input('min_total'));
        }
        if ($request->filled('max_total')) {
            $q->where('checkouts.total_price', '<=', (float) $request->input('max_total'));
        }
        if ($request->filled('before_value')) {
            $q->where('checkouts.total_before_discount', (float) $request->input('before_value'));
        }
        if ($request->filled('discount_value')) {
            $q->where('checkouts.discount_amount', (float) $request->input('discount_value'));
        }
        if ($request->filled('refund_value')) {
            $q->where('checkouts.refund_amount', (float) $request->input('refund_value'));
        }

        if ($refundFilter = $request->input('refund_filter')) {
            if ($refundFilter === 'yes') {
                $q->where(function ($sub) {
                    $sub->where('checkouts.refund_amount', '>', 0)
                        ->orWhere(DB::raw("LOWER(checkouts.status)"), 'refunded');
                });
            } elseif ($refundFilter === 'no') {
                $q->where(function ($sub) {
                    $sub->whereNull('checkouts.refund_amount')->orWhere('checkouts.refund_amount', '=', 0);
                });
            }
        }

        if ($searchOrder = $request->input('order_id')) {
            $q->where('checkouts.id', $searchOrder);
        }

        if ($userSearch = $request->input('user_search')) {
            $q->where(function ($sub) use ($userSearch) {
                $sub->where('users.id', $userSearch)
                    ->orWhere('users.email', 'like', '%' . $userSearch . '%')
                    ->orWhere('users.name', 'like', '%' . $userSearch . '%')
                    ->orWhere('checkouts.email', 'like', '%' . $userSearch . '%')
                    ->orWhere('checkouts.name', 'like', '%' . $userSearch . '%');
            });
        }

        if ($account = $request->input('account')) {
            if ($account === 'registered') {
                $q->whereNotNull('checkouts.user_id')->where('checkouts.user_id', '>', 0);
            } elseif ($account === 'guest') {
                $q->where(function ($sub) {
                    $sub->whereNull('checkouts.user_id')->orWhere('checkouts.user_id', '=', 0);
                });
            }
        }

        if ($couponCode = $request->input('coupon_code')) {
            $q->where('coupons.code', 'like', '%' . $couponCode . '%');
        }

        return [$q, $from, $to];
    }

    public function index(Request $request)
    {
        [$q, $from, $to] = $this->baseQuery($request);

        $orders = $q->select([
                'checkouts.id',
                'checkouts.user_id',
                'users.name as user_name',
                'users.email as user_email',
                'checkouts.name as guest_name',
                'checkouts.email as guest_email',
                'checkouts.status',
                'checkouts.total_before_discount',
                'checkouts.discount_amount',
                'checkouts.total_price',
                'checkouts.refund_amount',
                'coupons.code as coupon_code',
                'checkouts.created_at',
            ])
            ->orderByDesc('checkouts.created_at')
            ->paginate(25)
            ->withQueryString();

        // for filters
        $statuses = DB::table('checkouts')->distinct()->pluck('status')->filter()->values();

        // facets based on current filtered result (distinct values with counts)
        $facetStatus = (clone $q)
            ->select('checkouts.status as value', DB::raw('COUNT(*) as count'))
            ->groupBy('checkouts.status')
            ->reorder()
            ->orderByDesc('count')
            ->get();

        $facetCoupons = (clone $q)
            ->select('coupons.code as value', DB::raw('COUNT(*) as count'))
            ->groupBy('coupons.code')
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();

        $facetUsers = (clone $q)
            ->select('checkouts.user_id as value', 'users.name as name', DB::raw('COUNT(*) as count'))
            ->groupBy('checkouts.user_id', 'users.name')
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetTotal = (clone $q)->count();
        $facetAccount = collect([
            ['label' => 'Registered', 'value' => 'registered', 'count' => (clone $q)->whereNotNull('checkouts.user_id')->where('checkouts.user_id','>',0)->count()],
            ['label' => 'Guest', 'value' => 'guest', 'count' => (clone $q)->where(function($sub){ $sub->whereNull('checkouts.user_id')->orWhere('checkouts.user_id',0); })->count()],
        ]);
        $facetEmails = (clone $q)
            ->select(DB::raw("COALESCE(users.email, checkouts.email) as value"), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw("COALESCE(users.email, checkouts.email)"))
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetBefore = (clone $q)
            ->select('checkouts.total_before_discount as value', DB::raw('COUNT(*) as count'))
            ->groupBy('checkouts.total_before_discount')
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetDiscounts = (clone $q)
            ->select('checkouts.discount_amount as value', DB::raw('COUNT(*) as count'))
            ->groupBy('checkouts.discount_amount')
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetTotals = (clone $q)
            ->select('checkouts.total_price as value', DB::raw('COUNT(*) as count'))
            ->groupBy('checkouts.total_price')
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetRefunds = (clone $q)
            ->select('checkouts.refund_amount as value', DB::raw('COUNT(*) as count'))
            ->groupBy('checkouts.refund_amount')
            ->reorder()
            ->orderByDesc('count')
            ->limit(50)
            ->get();

        $summary = [
            'orders' => (clone $q)->count(),
            'net' => (clone $q)->sum('checkouts.total_price'),
            'discounts' => (clone $q)->sum('checkouts.discount_amount'),
            'refunds' => (clone $q)->sum('checkouts.refund_amount'),
            'with_coupon' => (clone $q)->whereNotNull('checkouts.coupon_id')->count(),
            'without_coupon' => (clone $q)->whereNull('checkouts.coupon_id')->count(),
            'registered' => (clone $q)->whereNotNull('checkouts.user_id')->where('checkouts.user_id','>',0)->count(),
            'guests' => (clone $q)->where(function($sub){ $sub->whereNull('checkouts.user_id')->orWhere('checkouts.user_id',0); })->count(),
            'top_status' => (clone $q)->select('checkouts.status', DB::raw('COUNT(*) as cnt'))
                ->groupBy('checkouts.status')
                ->reorder()
                ->orderByDesc('cnt')
                ->first(),
        ];
        $summary['aov'] = $summary['orders'] ? ($summary['net'] / $summary['orders']) : 0;

        return view('admins.reports.orders', [
            'orders' => $orders,
            'statuses' => $statuses,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'range' => $request->input('range'),
            'facets' => [
                'status' => $facetStatus,
                'coupon' => $facetCoupons,
                'user' => $facetUsers,
                'total' => $facetTotal,
                'account' => $facetAccount,
                'before' => $facetBefore,
                'discounts' => $facetDiscounts,
                'totals' => $facetTotals,
                'refunds' => $facetRefunds,
                'emails' => $facetEmails,
            ],
            'summary' => $summary,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$q, $from, $to] = $this->baseQuery($request);

        $rows = $q->select([
                'checkouts.id',
                'checkouts.user_id',
                'users.name as user_name',
                'users.email as user_email',
                'checkouts.name as guest_name',
                'checkouts.email as guest_email',
                'checkouts.status',
                'checkouts.total_before_discount',
                'checkouts.discount_amount',
                'checkouts.total_price',
                'checkouts.refund_amount',
                'coupons.code as coupon_code',
                'checkouts.created_at',
            ])
            ->orderByDesc('checkouts.created_at')
            ->get();

        $filename = 'orders_report_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order ID', 'User ID', 'User Name', 'Email', 'Status', 'Subtotal', 'Discount', 'Net total', 'Refund', 'Coupon code', 'Created at']);
            foreach ($rows as $r) {
                $subtotal = $r->total_before_discount ?? ($r->total_price + $r->discount_amount);
                fputcsv($out, [
                    $r->id,
                    $r->user_id,
                    $r->user_name,
                    $r->user_email,
                    $r->status,
                    $subtotal,
                    $r->discount_amount,
                    $r->total_price,
                    $r->refund_amount,
                    $r->coupon_code,
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
