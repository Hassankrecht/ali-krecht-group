<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostpayCouponAssigner
{
    /**
     * يربط كل كوبون عام من نوع postpay_auto بالمستخدم إذا كان الطلب مدفوعاً ويحقق الحد الأدنى.
     *
     * @param int        $userId
     * @param float|null $orderTotal   إجمالي الطلب للتحقق من min_total
     * @return array<\App\Models\Coupon> الكوبونات التي تم ربطها
     */
    public function assign(int $userId, ?float $orderTotal = null): array
    {
        $now = Carbon::now();

        $available = Coupon::whereNull('user_id')
            ->where('generated_for', 'postpay_auto')
            ->where('status', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiration_date')->orWhere('expiration_date', '>', $now);
            })
            ->orderBy('id')
            ->get();

        $assigned = [];

        foreach ($available as $coupon) {
            // تحقق من الحد الأدنى
            if (!is_null($orderTotal) && $coupon->min_total && $orderTotal < $coupon->min_total) {
                continue;
            }

            // لا تكرر الإرسال لنفس المستخدم من نفس القالب
            $alreadyDelivered = DB::table('coupon_deliveries')
                ->where('user_id', $userId)
                ->where('template_id', $coupon->id)
                ->exists();
            if ($alreadyDelivered) {
                continue;
            }

            // تحقق من سعة القالب (usage_limit للقالب = عدد المستخدمين/الإرسال المسموح)
            if ($coupon->usage_limit && $coupon->usage_limit > 0) {
                $deliveredCount = Coupon::where('template_id', $coupon->id)->count();
                if ($deliveredCount >= $coupon->usage_limit) {
                    continue;
                }
            }

            // أنشئ كوبوناً جديداً للمستخدم usage_limit = 1
            do {
                $code = strtoupper(Str::random(8));
            } while (Coupon::where('code', $code)->exists());

            $newCoupon = Coupon::create([
                'user_id'         => $userId,
                'template_id'     => $coupon->id,
                'code'            => $code,
                'type'            => $coupon->type,
                'value'           => $coupon->value,
                'usage_limit'     => $coupon->user_usage_limit ?? 1,
                'used_count'      => 0,
                'min_total'       => $coupon->min_total,
                'generated_for'   => 'postpay_auto',
                'starts_at'       => $coupon->starts_at ?: $now,
                'expiration_date' => $coupon->expiry_days ? Carbon::now()->addDays($coupon->expiry_days) : $coupon->expiration_date,
                'expiry_days'     => $coupon->expiry_days,
                'status'          => true,
            ]);

            DB::table('coupon_deliveries')->updateOrInsert(
                ['user_id' => $userId, 'template_id' => $coupon->id],
                ['delivered_at' => Carbon::now()]
            );

            $assigned[] = $newCoupon;
        }

        return $assigned;
    }
}
