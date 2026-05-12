<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Checkout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminCouponController extends Controller
{
    public function index()
    {
        $filters = [
            'generated_for' => request('generated_for'),
            'user_id'       => request('user_id'),
            'code'          => request('code'),
            'templates_only'=> request()->boolean('templates_only'),
            'status'        => request('status'),
            'type'          => request('type'),
            'sort'          => request('sort'),
            'dir'           => request('dir', 'desc'),
            'from'          => request('from'),
            'to'            => request('to'),
            'range'         => request('range'),
        ];

        $couponsQuery = Coupon::with('user');
        $now = Carbon::now();

        if ($filters['generated_for']) {
            $couponsQuery->where('generated_for', $filters['generated_for']);
        }
        if ($filters['user_id']) {
            $couponsQuery->where('user_id', $filters['user_id']);
        }
        if ($filters['templates_only']) {
            $couponsQuery->whereNull('user_id');
        }
        if ($filters['code']) {
            $couponsQuery->where('code', 'like', '%' . $filters['code'] . '%');
        }
        if ($filters['status'] === 'active') {
            $couponsQuery->where('status', true);
        } elseif ($filters['status'] === 'inactive') {
            $couponsQuery->where('status', false);
        }
        if (in_array($filters['type'], ['percent', 'fixed'])) {
            $couponsQuery->where('type', $filters['type']);
        }
        // Date range
        $from = $filters['from'];
        $to = $filters['to'];
        if ($filters['range']) {
            switch ($filters['range']) {
                case '1d':
                    $from = $now->copy()->subDay()->toDateString();
                    break;
                case 'week':
                    $from = $now->copy()->subWeek()->toDateString();
                    break;
                case 'month':
                    $from = $now->copy()->subMonth()->toDateString();
                    break;
                case '3m':
                    $from = $now->copy()->subMonths(3)->toDateString();
                    break;
                case '6m':
                    $from = $now->copy()->subMonths(6)->toDateString();
                    break;
                case '1y':
                    $from = $now->copy()->subYear()->toDateString();
                    break;
            }
        }
        if ($from) {
            $couponsQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $couponsQuery->whereDate('created_at', '<=', $to);
        }

        // Sort
        $allowedSorts = [
            'code' => 'code',
            'status' => 'status',
            'type' => 'type',
            'min_total' => 'min_total',
            'starts_at' => 'starts_at',
            'expiration_date' => 'expiration_date',
            'used_count' => 'used_count',
        ];
        $sortColumn = $allowedSorts[$filters['sort'] ?? ''] ?? 'created_at';
        $dir = strtolower($filters['dir']) === 'asc' ? 'asc' : 'desc';
        $couponsQuery->orderBy($sortColumn, $dir);

        // Facets based on كامل النتائج المفلترة
        $facetTotal = (clone $couponsQuery)->count();
        $facetCodes = (clone $couponsQuery)
            ->select('code', DB::raw('COUNT(*) as count'))
            ->groupBy('code')
            ->reorder()
            ->orderByDesc('count')
            ->get();
        $facetUsers = (clone $couponsQuery)
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->reorder()
            ->orderByDesc('count')
            ->get();
        $facetStatus = (clone $couponsQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->reorder()
            ->get();
        $facetTypes = (clone $couponsQuery)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->reorder()
            ->get();

        $coupons = $couponsQuery->paginate(15)->withQueryString();
        $baseForStats = (clone $couponsQuery);
        $total       = (clone $baseForStats)->count();
        $active      = (clone $baseForStats)->where('status', true)
            ->where(function($q) use ($now){
                $q->whereNull('expiration_date')->orWhere('expiration_date','>', $now);
            })->count();
        $expired     = (clone $baseForStats)->whereNotNull('expiration_date')->where('expiration_date','<=',$now)->count();
        $uniqueUsers = (clone $baseForStats)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        $usage = Checkout::select(
            'coupon_id',
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('COUNT(DISTINCT user_id) as users_count')
        )
            ->whereNotNull('coupon_id')
            ->groupBy('coupon_id')
            ->get()
            ->keyBy('coupon_id');

        $usageUsers = Checkout::with('user')
            ->whereNotNull('coupon_id')
            ->select(
                'coupon_id',
                'user_id',
                DB::raw('COUNT(*) as uses'),
                DB::raw('MAX(created_at) as last_used_at')
            )
            ->groupBy('coupon_id','user_id')
            ->get()
            ->groupBy('coupon_id');

        return view('admins.coupons.index', compact(
            'coupons','total','active','expired','uniqueUsers','filters','usage','usageUsers',
            'facetCodes','facetUsers','facetStatus','facetTypes','facetTotal'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'            => 'nullable|string|max:50|unique:coupons,code',
            'type'            => 'required|in:percent,fixed',
            'value'           => 'required|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'starts_at'       => 'nullable|date',
            'usage_limit'     => 'nullable|integer|min:0',
            'user_usage_limit'=> 'nullable|integer|min:1',
            'min_total'       => 'nullable|numeric|min:0',
            'status'          => 'nullable|boolean',
            'generated_for'   => 'nullable|string|max:50',
            'user_id'         => 'nullable|integer|exists:users,id',
            'expiry_days'     => 'nullable|integer|min:1',
        ]);

        if (empty($data['code'])) {
            do {
                $data['code'] = strtoupper(Str::random(8));
            } while (Coupon::where('code', $data['code'])->exists());
        }

        $data['status'] = $request->boolean('status', true);
        $data['usage_limit'] = $data['usage_limit'] ?? 1;
        $data['user_usage_limit'] = $data['user_usage_limit'] ?? 1;
        $data['used_count'] = 0;
        $data['generated_for'] = $request->input('generated_for', 'manual');

        Coupon::create($data);

        return back()->with('success', 'Coupon created.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        // حالة التفعيل/التعطيل السريع
        if (!$request->has('type')) {
            $coupon->status = $request->boolean('status', $coupon->status);
            $coupon->save();
            return back()->with('success', 'Coupon status updated.');
        }

        $data = $request->validate([
            'type'            => 'required|in:percent,fixed',
            'value'           => 'required|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'starts_at'       => 'nullable|date',
            'usage_limit'     => 'nullable|integer|min:0',
            'user_usage_limit'=> 'nullable|integer|min:1',
            'min_total'       => 'nullable|numeric|min:0',
            'status'          => 'nullable|boolean',
            'generated_for'   => 'nullable|string|max:50',
            'user_id'         => 'nullable|integer|exists:users,id',
            'expiry_days'     => 'nullable|integer|min:1',
        ]);

        $data['status'] = $request->boolean('status', true);
        $data['user_usage_limit'] = $data['user_usage_limit'] ?? 1;
        $coupon->update($data);

        return back()->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
