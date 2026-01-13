<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
$coupons = Coupon::orderBy("id")->get(["id","code","user_id","generated_for","status","min_total","usage_limit","used_count","starts_at","expiration_date","expiry_days"]);
foreach($coupons as $c){
  echo "#{$c->id} code={$c->code} user={$c->user_id} gen={$c->generated_for} status={$c->status} min={$c->min_total} starts={$c->starts_at} expires={$c->expiration_date} expiry_days={$c->expiry_days}\n";
}
