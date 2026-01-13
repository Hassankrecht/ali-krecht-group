<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
require __DIR__."/vendor/autoload.php";
$app = require __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Coupon;
$c = Coupon::find(57);
var_export($c ? $c->toArray() : null);
