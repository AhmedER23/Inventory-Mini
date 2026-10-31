<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalTransactions = StockTransaction::count();
        $lowStockQuery = Product::whereColumn('quantity', '<=', 'low_stock_threshold');
        $lowStockProducts = (clone $lowStockQuery)->count();
        $lowStockItems = (clone $lowStockQuery)->latest()->take(5)->get();
        $recentTransactions = StockTransaction::with('product')->latest()->take(6)->get();
        $allProducts = Product::orderBy('name')->get(['id', 'name', 'sku', 'quantity', 'unit']);

        return view('dashboard', compact(
            'totalProducts',
            'totalTransactions',
            'lowStockProducts',
            'lowStockItems',
            'recentTransactions',
            'allProducts'
        ));
    }
}
