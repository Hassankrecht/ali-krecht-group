<?php

// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Project;
use App\Models\Product;
use App\Models\PageVisit;

class DashboardController extends Controller
{
    public function index()
    {
        $ordersCount   = Checkout::count();
        $projectsCount = Project::count();
        $productsCount = Product::count();
        $visitsToday   = PageVisit::whereDate('created_at', today())->count();

        return view('admins.dashboard', compact(
            'ordersCount',
            'projectsCount',
            'productsCount',
            'visitsToday'
        ));
    }
}
