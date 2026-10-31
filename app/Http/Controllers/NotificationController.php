<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'low_stock_threshold')->get();

        return view('notifications.index', compact('lowStockProducts'));
    }
}
