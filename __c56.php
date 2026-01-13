<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
$c = Coupon::find(56);
var_export($c ? $c->toArray() : null);
