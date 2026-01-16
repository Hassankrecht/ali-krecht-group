<?php

namespace App\Services;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WelcomeCouponAssigner
{
    /**
     * يمنح كوبون ترحيبي مبني على قالب عام لكل مستخدم مرة واحدة.
     */
    public function assign(int $userId): void
    {
        $now = Carbon::now();

        // إذا كان لدى المستخدم أي كوبون ترحيبي سابق، لا ترسل جديداً حتى لو وُجدت قوالب أخرى
        $hasAnyWelcome = Coupon::where('user_id', $userId)
            ->where('generated_for', 'welcome_auto')
            ->exists();
        if ($hasAnyWelcome) {
            return;
        }

        // ابحث عن كوبون عام غير مخصص (user_id null) من نوع welcome_auto
        $coupon = Coupon::whereNull('user_id')
            ->where('generated_for', 'welcome_auto')
            ->where('status', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiration_date')->orWhere('expiration_date', '>', $now);
            })
            ->orderBy('id')
            ->first();

        if (!$coupon) {
            return;
        }

        // تحقق من عدم تسليم نفس القالب للمستخدم سابقاً
        $alreadyDelivered = DB::table('coupon_deliveries')
            ->where('user_id', $userId)
            ->where('template_id', $coupon->id)
            ->exists();
        if ($alreadyDelivered) {
            return;
        }

        // تحقق من سعة القالب (usage_limit للقالب = عدد المستخدمين المسموح لهم)
        if ($coupon->usage_limit && $coupon->usage_limit > 0) {
            $deliveredCount = Coupon::where('template_id', $coupon->id)->count();
            if ($deliveredCount >= $coupon->usage_limit) {
                return;
            }
        }

        // أنشئ كوبوناً خاصاً بالمستخدم usage_limit = 1
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        Coupon::create([
            'user_id'         => $userId,
            'template_id'     => $coupon->id,
            'code'            => $code,
            'type'            => $coupon->type,
            'value'           => $coupon->value,
            'usage_limit'     => $coupon->user_usage_limit ?? 1,
            'used_count'      => 0,
            'min_total'       => $coupon->min_total,
            'generated_for'   => 'welcome_auto',
            'starts_at'       => $coupon->starts_at ?: $now,
            'expiration_date' => $coupon->expiry_days ? Carbon::now()->addDays($coupon->expiry_days) : $coupon->expiration_date,
            'expiry_days'     => $coupon->expiry_days,
            'status'          => true,
        ]);

        // سجّل عملية التسليم حتى لا تتكرر
        DB::table('coupon_deliveries')->updateOrInsert(
            ['user_id' => $userId, 'template_id' => $coupon->id],
            ['delivered_at' => Carbon::now()]
        );
    }
}
