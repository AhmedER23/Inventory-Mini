<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /** Rapor ekranindaki secilebilir periyotlar (gun) */
    public const PERIODS = [7, 30, 90, 365];

    public function index(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = max(1, min(365, $days));
        $from = now()->subDays($days);

        $useCache = filter_var(Setting::get('cache_reports', '1'), FILTER_VALIDATE_BOOLEAN);

        $topMovingQuery = function () use ($from) {
            return StockTransaction::query()
                ->select([
                    'product_id',
                    DB::raw('COUNT(*) as tx_count'),
                    DB::raw('SUM(qty) as total_qty'),
                    DB::raw("SUM(CASE WHEN type='IN' THEN qty ELSE 0 END) as in_qty"),
                    DB::raw("SUM(CASE WHEN type='OUT' THEN qty ELSE 0 END) as out_qty"),
                ])
                ->where('created_at', '>=', $from)
                ->groupBy('product_id')
                ->orderByDesc('tx_count')
                ->with('product:id,sku,name,quantity,low_stock_threshold')
                ->limit(20)
                ->get();
        };

        $lowStockQuery = function () {
            return Product::query()
                ->whereColumn('quantity', '<=', 'low_stock_threshold')
                ->orderBy('quantity')
                ->get();
        };

        if ($useCache) {
            $topMoving = Cache::remember("reports.top_moving.{$days}", 300, $topMovingQuery);
            $lowStock = Cache::remember('reports.low_stock', 300, $lowStockQuery);
        } else {
            $topMoving = $topMovingQuery();
            $lowStock = $lowStockQuery();
        }

        $periodStats = StockTransaction::query()
            ->where('created_at', '>=', $from)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'IN' THEN qty ELSE 0 END), 0) as in_qty")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'OUT' THEN qty ELSE 0 END), 0) as out_qty")
            ->first();

        $totalProducts = Product::count();

        return view('reports.index', [
            'topMoving' => $topMoving,
            'lowStock' => $lowStock,
            'days' => $days,
            'periodStats' => $periodStats,
            'totalProducts' => $totalProducts,
        ]);
    }

    /**
     * Stok/urun degisikliklerinden sonra rapor cache anahtarlarini temizler.
     */
    public static function forgetCache(): void
    {
        Cache::forget('reports.low_stock');

        foreach (self::PERIODS as $period) {
            Cache::forget("reports.top_moving.{$period}");
        }

    }
}
