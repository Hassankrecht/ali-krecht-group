<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',            // كود الخصم
        'type',            // نسبة مئوية أو مبلغ ثابت
        'value',           // قيمة الخصم
        'expiration_date', // تاريخ انتهاء الصلاحية
        'status',          // active / inactive
    ];
}
