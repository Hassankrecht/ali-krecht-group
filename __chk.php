<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
$code="FYEXZHND";
$c = Coupon::where("code",$code)->first();
if(!$c){ echo "not found"; exit; }
print_r($c->toArray());
