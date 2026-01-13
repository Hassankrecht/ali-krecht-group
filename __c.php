<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
$uid = 7;
$coupons = Coupon::where('user_id',$uid)->orderBy('id')->get(['id','code','status','type','value','min_total','usage_limit','used_count','starts_at','expiration_date']);
foreach($coupons as $c){
  echo "#{$c->id} code={$c->code} status={$c->status} used={$c->used_count}/{$c->usage_limit} min={$c->min_total} start={$c->starts_at} exp={$c->expiration_date}\n";
}
