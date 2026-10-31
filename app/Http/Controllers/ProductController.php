<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function show(string $locale, Product $product)
    {
        $transactions = $product->stockTransactions()->latest()->paginate(10);

        return view('products.show', compact('product', 'transactions'));
    }

    public function store(Request $request)
    {
        if ($request->has('sku')) {
            $request->merge(['sku' => strtoupper(trim((string) $request->input('sku')))]);
        }

        $data = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku', 'regex:/^[A-Z0-9\-_]+$/'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:0', 'max:4000000000'],
            'unit' => ['nullable', 'string', 'max:30'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:4000000000'],
        ]);

        $data['unit'] = !empty($data['unit']) ? $data['unit'] : 'Adet';

        if (! isset($data['low_stock_threshold'])) {
            $data['low_stock_threshold'] = (int) Setting::get('default_low_stock_threshold', 5);
        }

        $product = DB::transaction(function () use ($data) {
            $product = Product::create($data);

            if ($product->quantity > 0) {
                StockTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'IN',
                    'qty' => $product->quantity,
                    'note' => 'Initial stock',
                    'before_qty' => 0,
                    'after_qty' => $product->quantity,
                ]);
            }

            return $product;
        });

        ReportController::forgetCache();

        return redirect()->route('products.index')
            ->with('success', __('ui.Product created successfully.'));
    }

    public function update(Request $request, string $locale, Product $product)
    {
        if ($request->has('sku')) {
            $request->merge(['sku' => strtoupper(trim((string) $request->input('sku')))]);
        }

        $data = $request->validate([
            'sku' => [
                'required', 'string', 'max:50', 'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['required', 'integer', 'min:0', 'max:4000000000'],
            'unit' => ['nullable', 'string', 'max:30'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:4000000000'],
        ]);

        $data['unit'] = !empty($data['unit']) ? $data['unit'] : ($product->unit ?: 'Adet');

        DB::transaction(function () use ($data, $product) {
            $before = (int) $product->quantity;
            $after = (int) $data['quantity'];

            $product->update($data);

            if ($after !== $before) {
                StockTransaction::create([
                    'product_id' => $product->id,
                    'type' => $after > $before ? 'IN' : 'OUT',
                    'qty' => abs($after - $before),
                    'note' => 'Manual adjustment',
                    'before_qty' => $before,
                    'after_qty' => $after,
                ]);
            }
        });

        ReportController::forgetCache();

        return redirect()->route('products.index')
            ->with('success', __('ui.Product updated successfully.'));
    }

    public function destroy(Request $request, string $locale, Product $product)
    {
        $product->delete();

        ReportController::forgetCache();

        return redirect()->route('products.index')
            ->with('success', __('ui.Product deleted successfully.'));
    }
}
