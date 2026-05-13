<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Services\PostpayCouponAssigner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminOrderController extends Controller
{
    protected array $statuses = ['Pending', 'Paid', 'Shipped', 'Cancelled', 'Refunded', 'Completed'];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $from   = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null;
        $to     = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : null;
        $range  = $request->query('range');
        $q      = $request->query('q');
        $hasCoupon = $request->query('has_coupon');
        $sort   = $request->query('sort');
        $fName  = $request->query('f_name');
        $fEmail = $request->query('f_email');
        $fTotal = $request->query('f_total');
        $fDiscount = $request->query('f_discount');
        $fCoupon = $request->query('f_coupon');
        $fAccount = $request->query('f_account');
        $fPlatform = $request->query('f_platform');

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

        // fallback defaults to today
        $from = $from ?: Carbon::now()->startOfDay();
        $to   = $to   ?: Carbon::now()->endOfDay();

        $ordersQuery = Checkout::with(['user', 'coupon'])
            ->when($status, fn($q) => $q->where('checkouts.status', $status))
            ->when($from, fn($q) => $q->whereDate('checkouts.created_at', '>=', Carbon::parse($from)))
            ->when($to, fn($q) => $q->whereDate('checkouts.created_at', '<=', Carbon::parse($to)))
            ->when($q, function($query) use ($q) {
                $query->where(function($qq) use ($q){
                    $qq->where('checkouts.id', $q)
                       ->orWhere('checkouts.name', 'like', "%{$q}%")
                       ->orWhere('checkouts.email', 'like', "%{$q}%")
                       ->orWhereHas('coupon', fn($c) => $c->where('code', 'like', "%{$q}%"));
                });
            })
            ->when($hasCoupon === 'with', fn($qq) => $qq->whereNotNull('coupon_id'))
            ->when($hasCoupon === 'without', fn($qq) => $qq->whereNull('coupon_id'));
        // Per-column filters
        if ($fName) {
            $ordersQuery->where('name', $fName);
        }
        if ($fEmail) {
            $ordersQuery->where('email', $fEmail);
        }
        if ($fTotal !== null && $fTotal !== '') {
            $ordersQuery->where('total_price', $fTotal);
        }
        if ($fDiscount !== null && $fDiscount !== '') {
            $ordersQuery->where('discount_amount', $fDiscount);
        }
        if ($fCoupon) {
            if ($fCoupon === 'none') {
                $ordersQuery->whereNull('coupon_id');
            } else {
                $ordersQuery->whereHas('coupon', fn($c) => $c->where('code', $fCoupon));
            }
        }
        if ($fAccount) {
            if ($fAccount === 'registered') {
                $ordersQuery->whereNotNull('user_id')->where('user_id', '>', 0);
            } elseif ($fAccount === 'guest') {
                $ordersQuery->where(function($sub){ $sub->whereNull('user_id')->orWhere('user_id', 0); });
            }
        }
        if ($fPlatform) {
            if (in_array($fPlatform, ['web', 'android', 'ios'], true)) {
                $ordersQuery->where('checkouts.source_platform', $fPlatform);
            } elseif ($fPlatform === 'unknown') {
                $ordersQuery->where(function($sub){
                    $sub->whereNull('checkouts.source_platform')->orWhere('checkouts.source_platform', '');
                });
            }
        }

        // ترتيب
        switch ($sort) {
            case 'total_asc':
                $ordersQuery->orderBy('total_price', 'asc');
                break;
            case 'total_desc':
                $ordersQuery->orderBy('total_price', 'desc');
                break;
            case 'date_asc':
                $ordersQuery->orderBy('id', 'asc');
                break;
            default:
                $ordersQuery->orderBy('id', 'desc');
        }

        $orders = (clone $ordersQuery)
            ->paginate(15)
            ->appends(request()->query());

        // إعادة ضبط الترتيب لتجنب only_full_group_by في التجميعات
        $aggQuery = (clone $ordersQuery);
        $aggQuery->getQuery()->orders = null;

        // Facets for trigger buttons
        $facetNames = (clone $aggQuery)
            ->select('name as value', DB::raw('COUNT(*) as count'))
            ->groupBy('name')
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetEmails = (clone $aggQuery)
            ->select('email as value', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetTotals = (clone $aggQuery)
            ->select('total_price as value', DB::raw('COUNT(*) as count'))
            ->groupBy('total_price')
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetDiscounts = (clone $aggQuery)
            ->select('discount_amount as value', DB::raw('COUNT(*) as count'))
            ->groupBy('discount_amount')
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetCoupons = (clone $aggQuery)
            ->leftJoin('coupons', 'coupons.id', '=', 'checkouts.coupon_id')
            ->select('coupons.code as value', DB::raw('COUNT(*) as count'))
            ->groupBy('coupons.code')
            ->orderByDesc('count')
            ->limit(50)
            ->get();
        $facetAccount = collect([
            ['label'=>'Registered','value'=>'registered','count'=>(clone $aggQuery)->whereNotNull('user_id')->where('user_id','>',0)->count()],
            ['label'=>'Guest','value'=>'guest','count'=>(clone $aggQuery)->where(function($sub){ $sub->whereNull('user_id')->orWhere('user_id',0); })->count()],
        ]);
        $facetPlatforms = (clone $aggQuery)
            ->selectRaw("CASE WHEN checkouts.source_platform IS NULL OR checkouts.source_platform = '' THEN 'unknown' ELSE checkouts.source_platform END as value")
            ->selectRaw('COUNT(*) as count')
            ->groupBy('checkouts.source_platform')
            ->orderByDesc('count')
            ->get();

        // إحصاءات سريعة
        $totalOrders     = (clone $aggQuery)->count();
        $statusCounts    = (clone $aggQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $customersCount  = (clone $aggQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $withCouponCount = (clone $aggQuery)->whereNotNull('coupon_id')->count();
        $distinctCoupons = (clone $aggQuery)->whereNotNull('coupon_id')->distinct('coupon_id')->count('coupon_id');
        $discountSum     = (clone $aggQuery)->sum('discount_amount');
        $totalAmount     = (clone $aggQuery)->sum('total_price');

        return view('admins.orders.index', [
            'orders' => $orders,
            'statuses' => $this->statuses,
            'filterStatus' => $status,
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
            'range' => $range,
            'search' => $q,
            'hasCoupon' => $hasCoupon,
            'sort' => $sort,
            'totalOrders' => $totalOrders,
            'statusCounts' => $statusCounts,
            'customersCount' => $customersCount,
            'withCouponCount' => $withCouponCount,
            'distinctCoupons' => $distinctCoupons,
            'discountSum' => $discountSum,
            'totalAmount' => $totalAmount,
            'facets' => [
                'names' => $facetNames,
                'emails' => $facetEmails,
                'totals' => $facetTotals,
                'discounts' => $facetDiscounts,
                'coupons' => $facetCoupons,
                'account' => $facetAccount,
                'platforms' => $facetPlatforms,
            ],
            'f' => [
                'name' => $fName,
                'email' => $fEmail,
                'total' => $fTotal,
                'discount' => $fDiscount,
            'coupon' => $fCoupon,
                'account' => $fAccount,
                'platform' => $fPlatform,
            ],
        ]);
    }

    public function show(Checkout $order)
    {
        $order->load(['user', 'coupon', 'items']);

        return view('admins.orders.show', [
            'order' => $order,
            'statuses' => $this->statuses,
        ]);
    }

    public function refund(Request $request, Checkout $order)
    {
        $hasPaidAt = Schema::hasColumn('checkouts', 'paid_at');
        $isPaid = $hasPaidAt ? !is_null($order->paid_at) : ($order->status === 'Paid');

        // الرفاند متاح فقط إذا تم الدفع فعلاً
        if (!$isPaid) {
            return back()->withErrors(['refund' => 'Refund allowed only after payment (paid_at required).']);
        }
        // يجب أن يكون الطلب مدفوعاً ثم مُلغى أو سيتم إلغاؤه الآن
        if (!in_array($order->status, ['Paid', 'Cancelled'])) {
            return back()->withErrors(['refund' => 'Refund available only for paid or cancelled orders.']);
        }

        $data = $request->validate([
            'refund_amount' => 'required|numeric|min:0',
        ]);

        // إذا كان الطلب ما زال "Paid" نحوله إلى "Cancelled" مع حفظ الرفاند
        if ($order->status === 'Paid') {
            $order->status = 'Cancelled';
        }

        $order->refund_amount = $data['refund_amount'];
        if (Schema::hasColumn('checkouts', 'refunded_at')) {
            $order->refunded_at = now();
        }
        $order->save();

        return back()->with('success', 'Refund recorded.');
    }

    public function update(Request $request, Checkout $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->statuses),
        ]);

        $oldStatus = $order->status;
        $order->status = $validated['status'];

        // إذا تحولت الحالة إلى Paid ولم يكن هناك paid_at، نسجله الآن
        if ($order->status === 'Paid' && Schema::hasColumn('checkouts', 'paid_at') && empty($order->paid_at)) {
            $order->paid_at = now();
        }

        $order->save();

        if ($validated['status'] === 'Paid' && $oldStatus !== 'Paid' && $order->user_id) {
            $total = $order->total_before_discount ?? $order->total_price;
            app(PostpayCouponAssigner::class)->assign($order->user_id, $total);
        }

        return back()->with('success', 'Order updated successfully.');
    }

    public function destroy(Checkout $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }

    public function export(Request $request)
    {
        $status = $request->query('status');
        $from   = $request->query('from');
        $to     = $request->query('to');
        $range  = $request->query('range');
        $q      = $request->query('q');
        $hasCoupon = $request->query('has_coupon');

        if ($range) {
            $today = Carbon::today();
            $end   = $today->copy()->endOfDay();
            $start = null;
            switch ($range) {
                case 'today': $start = $today->copy()->startOfDay(); break;
                case 'last_7': $start = $today->copy()->subDays(6)->startOfDay(); break;
                case 'last_30': $start = $today->copy()->subDays(29)->startOfDay(); break;
                case 'last_month':
                    $start = $today->copy()->subMonth()->startOfMonth();
                    $end   = $today->copy()->subMonth()->endOfMonth();
                    break;
                case 'last_6m': $start = $today->copy()->subMonthsNoOverflow(6)->startOfDay(); break;
                case 'last_year': $start = $today->copy()->subYear()->startOfDay(); break;
            }
            if ($start) { $from = $start->toDateString(); }
            if ($end)   { $to   = $end->toDateString(); }
        }

        $orders = Checkout::with(['user', 'coupon'])
            ->when($status, fn($q) => $q->where('checkouts.status', $status))
            ->when($from, fn($q) => $q->whereDate('checkouts.created_at', '>=', Carbon::parse($from)))
            ->when($to, fn($q) => $q->whereDate('checkouts.created_at', '<=', Carbon::parse($to)))
            ->when($q, function($query) use ($q) {
                $query->where(function($qq) use ($q){
                    $qq->where('checkouts.id', $q)
                       ->orWhere('checkouts.name', 'like', "%{$q}%")
                       ->orWhere('checkouts.email', 'like', "%{$q}%")
                       ->orWhereHas('coupon', fn($c) => $c->where('code', 'like', "%{$q}%"));
                });
            })
            ->when($hasCoupon === 'with', fn($qq) => $qq->whereNotNull('coupon_id'))
            ->when($hasCoupon === 'without', fn($qq) => $qq->whereNull('coupon_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_export_' . now()->format('Ymd_His') . '.csv';
        
        $callback = function() use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order ID', 'Customer', 'Email', 'Phone', 'Status', 'Total', 'Discount', 'Coupon', 'Created At']);
            
            foreach ($orders as $order) {
                fputcsv($out, [
                    $order->id,
                    $order->name,
                    $order->email,
                    $order->phone ?? '',
                    $order->status,
                    $order->total_price,
                    $order->discount_amount ?? 0,
                    $order->coupon?->code ?? '',
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|json',
            'status' => 'required|in:' . implode(',', $this->statuses),
        ]);

        $ids = json_decode($validated['ids']);
        $newStatus = $validated['status'];

        $orders = Checkout::whereIn('id', $ids)->get();
        
        foreach ($orders as $order) {
            $oldStatus = $order->status;
            $order->status = $newStatus;

            if ($newStatus === 'Paid' && Schema::hasColumn('checkouts', 'paid_at') && empty($order->paid_at)) {
                $order->paid_at = now();
            }

            $order->save();

            if ($newStatus === 'Paid' && $oldStatus !== 'Paid' && $order->user_id) {
                $total = $order->total_before_discount ?? $order->total_price;
                app(PostpayCouponAssigner::class)->assign($order->user_id, $total);
            }
        }

        return redirect()->route('admin.orders.index')->with('success', count($ids) . ' order(s) updated successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|json',
        ]);

        $ids = json_decode($validated['ids']);
        Checkout::whereIn('id', $ids)->delete();

        return redirect()->route('admin.orders.index')->with('success', count($ids) . ' order(s) deleted successfully.');
    }
}

