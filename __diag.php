<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
use App\Models\Checkout;
use Illuminate\Support\Facades\DB;

$templates = Coupon::whereNull('user_id')->get(['id','code','generated_for','status','min_total','usage_limit','expiration_date','starts_at','expiry_days']);
$userCoupons = Coupon::where('user_id',2)->orderBy('id')->get(['id','code','generated_for','status','used_count','usage_limit','min_total','expiration_date','starts_at']);
$orders = Checkout::where('user_id',2)->orderByDesc('id')->take(5)->get(['id','status','total_price','total_before_discount']);

echo "Templates:\n";
foreach($templates as $t){
  echo "#{$t->id} {$t->generated_for} code={$t->code} status={$t->status} min={$t->min_total} usage_limit={$t->usage_limit} exp={$t->expiration_date} starts={$t->starts_at} expiry_days={$t->expiry_days}\n";
}

echo "\nUser 2 coupons:\n";
foreach($userCoupons as $c){
  echo "#{$c->id} {$c->generated_for} code={$c->code} status={$c->status} used={$c->used_count}/{$c->usage_limit} min={$c->min_total} exp={$c->expiration_date} starts={$c->starts_at}\n";
}

echo "\nRecent orders for user 2:\n";
foreach($orders as $o){
  echo "order #{$o->id} status={$o->status} total_before={$o->total_before_discount} total={$o->total_price}\n";
}
