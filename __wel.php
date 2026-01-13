<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

$templates = Coupon::whereNull('user_id')->where('generated_for','welcome_auto')->get(['id','code','status','usage_limit','user_usage_limit','min_total','expiration_date','starts_at','expiry_days']);
$deliveries = DB::table('coupon_deliveries')->whereIn('template_id', $templates->pluck('id'))->get();
$users = DB::table('users')->orderByDesc('id')->limit(5)->get(['id','email','created_at']);
$userCoupons = Coupon::whereNotNull('user_id')->where('generated_for','welcome_auto')->get(['id','user_id','code','status','used_count','usage_limit','expiration_date','starts_at','template_id']);

echo "Welcome templates:\n";
foreach($templates as $t){
  echo "#{$t->id} code={$t->code} status={$t->status} usage_limit={$t->usage_limit} per_user={$t->user_usage_limit} exp={$t->expiration_date} starts={$t->starts_at} expiry_days={$t->expiry_days}\n";
}

echo "\nDeliveries:\n";
foreach($deliveries as $d){
  echo "user={$d->user_id} template={$d->template_id} at={$d->delivered_at}\n";
}

echo "\nUser welcome coupons:\n";
foreach($userCoupons as $c){
  echo "user={$c->user_id} code={$c->code} template={$c->template_id} status={$c->status} used={$c->used_count}/{$c->usage_limit} exp={$c->expiration_date} starts={$c->starts_at}\n";
}

echo "\nRecent users:\n";
foreach($users as $u){
  echo "id={$u->id} email={$u->email} created={$u->created_at}\n";
}
