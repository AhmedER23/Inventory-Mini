<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    public function index()
    {
        $transactions = StockTransaction::with('product')->latest()->get();
        $products = Product::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:IN,OUT'],
            'qty' => ['required', 'integer', 'min:1', 'max:4000000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::lockForUpdate()->find($data['product_id']);

        if (! $product) {
            return redirect()->back(fallback: route('transactions.index'))
                ->withInput()
                ->with('error', __('ui.Product not found.'));
        }

        if ($data['type'] === 'OUT' && $data['qty'] > $product->quantity) {
            $msg = __('ui.Cannot withdraw more than available stock.');

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $msg,
                    'errors' => ['qty' => [$msg]],
                ], 422);
            }

            return redirect()->back(fallback: route('transactions.index'))
                ->withInput()
                ->with('error', $msg)
                ->withErrors(['qty' => $msg]);
        }

        DB::transaction(function () use ($data, $product) {
            $before = $product->quantity;
            $after = $data['type'] === 'IN' ? $before + $data['qty'] : $before - $data['qty'];

            $product->update(['quantity' => $after]);

            StockTransaction::create([
                'product_id' => $product->id,
                'type' => $data['type'],
                'qty' => $data['qty'],
                'note' => $data['note'] ?? null,
                'before_qty' => $before,
                'after_qty' => $after,
            ]);
        });

        ReportController::forgetCache();

        return redirect()->back(fallback: route('transactions.index'))
            ->with('success', __('ui.Transaction recorded successfully.'));
    }
}
