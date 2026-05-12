<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'name',
        'email',
        'phone_number',
        'town',
        'country',
        'zipcode',
        'address',
        'total_price',
        'total_before_discount',
        'discount_amount',
        'delivery_fee',
        'payment_method',
        'order_note',
        'source_platform',
        'status', // Pending, Paid, Shipped, Cancelled
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CheckoutItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
