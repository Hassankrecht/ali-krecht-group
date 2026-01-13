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
    protected array $statuses = ['Pending', 'Paid', 'Shipped', 'Cancelled'];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $from   = $request->query('from');
        $to     = $request->query('to');
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

        if ($range) {
            $today = Carbon::today();
            $end   = $today->copy()->endOfDay();
            $start = null;
            switch ($range) {
                case 'today':
                    $start = $today->copy()->startOfDay();
                    break;
                case 'last_7':
                    $start = $today->copy()->subDays(6)->startOfDay();
                    break;
                case 'last_30':
                    $start = $today->copy()->subDays(29)->startOfDay();
                    break;
                case 'last_month':
                    $start = $today->copy()->subMonth()->startOfMonth();
                    $end   = $today->copy()->subMonth()->endOfMonth();
                    break;
                case 'last_6m':
                    $start = $today->copy()->subMonthsNoOverflow(6)->startOfDay();
                    break;
                case 'last_year':
                    $start = $today->copy()->subYear()->startOfDay();
                    break;
                default:
                    $start = null;
                    $end   = null;
            }
            if ($start) { $from = $start->toDateString(); }
            if ($end)   { $to   = $end->toDateString(); }
        }

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
            ->withQueryString();

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
            'dateFrom' => $from,
            'dateTo' => $to,
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
            ],
            'f' => [
                'name' => $fName,
                'email' => $fEmail,
                'total' => $fTotal,
                'discount' => $fDiscount,
            'coupon' => $fCoupon,
                'account' => $fAccount,
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
}
