<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutoCouponService
{
    /**
     * توزع كل القوالب النشطة من نوع محدد على مستخدم واحد، مع احترام الحد الأدنى والمواعيد.
     *
     * @param int         $userId
     * @param string      $templateType  welcome_auto | postpay_auto
     * @param float|null  $orderTotal    إجمالي الطلب لاختيار القوالب التي ينطبق عليها الحد الأدنى
     * @param bool        $sendEmail
     * @return array<\App\Models\Coupon>  الكوبونات التي أُنشئت للمستخدم
     */
    public function distribute(int $userId, string $templateType, ?float $orderTotal = null, bool $sendEmail = true): array
    {
        // تم إيقاف التوليد التلقائي حسب طلبك: لا إنشاء، لا إرسال.
        return [];
    }

    protected function hasDelivery(int $userId, int $templateId): bool
    {
        return DB::table('coupon_deliveries')
            ->where('user_id', $userId)
            ->where('template_id', $templateId)
            ->exists();
    }

    protected function logDelivery(int $userId, int $templateId): void
    {
        DB::table('coupon_deliveries')->updateOrInsert(
            ['user_id' => $userId, 'template_id' => $templateId],
            ['delivered_at' => Carbon::now()]
        );
    }

    protected function sendEmail(?string $email, Coupon $coupon): void
    {
        if (!$email) {
            return;
        }

        try {
            $msg = "Here is your coupon: {$coupon->code}\n"
                . "Discount: " . ($coupon->type === 'percent' ? $coupon->value . "% off" : '$' . $coupon->value . ' off') . "\n"
                . ($coupon->min_total ? "Min order: $" . number_format($coupon->min_total, 2) . "\n" : '')
                . ($coupon->expiration_date ? "Valid until: " . Carbon::parse($coupon->expiration_date)->toDateString() . "\n" : '');

            Mail::raw($msg, function ($m) use ($email) {
                $m->to($email)->subject('Your coupon');
            });
        } catch (\Throwable $e) {
            // لا تعطل بقية العملية إذا فشل الإيميل
        }
    }
}
