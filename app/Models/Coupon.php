<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',            // كود الخصم
        'type',            // نسبة مئوية أو مبلغ ثابت
        'value',           // قيمة الخصم
        'expiration_date', // تاريخ انتهاء الصلاحية
        'status',          // active / inactive
        'user_id',
        'usage_limit',
        'user_usage_limit',
        'used_count',
        'min_total',
        'generated_for',
        'starts_at',
        'expiry_days',
        'template_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
