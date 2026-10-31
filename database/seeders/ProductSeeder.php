<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Ürün 1', 'SKU-1001', 0, 2],
            ['Ürün 2', 'SKU-1002', 48, 10],
            ['Ürün 3', 'SKU-1003', 25, 5],
            ['Ürün 4', 'SKU-1004', 0, 10],
            ['Ürün 5', 'SKU-1005', 120, 20],
            ['Ürün 6', 'SKU-1006', 64, 15],
            ['Ürün 7', 'SKU-1007', 33, 8],
            ['Ürün 8', 'SKU-1008', 90, 10],
            ['Ürün 9', 'SKU-1009', 15, 5],
            ['Ürün 10', 'SKU-1010', 3, 5],
            ['Ürün 11', 'SKU-1011', 72, 12],
        ];

        foreach ($products as [$name, $sku, $quantity, $threshold]) {
            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'description' => $name.' için örnek ürün açıklaması.',
                    'quantity' => $quantity,
                    'unit' => 'Adet',
                    'low_stock_threshold' => $threshold,
                ]
            );
        }

        $product = Product::where('sku', 'SKU-1010')->first();

        if ($product && StockTransaction::where('product_id', $product->id)->doesntExist()) {
            StockTransaction::create([
                'product_id' => $product->id,
                'type' => 'IN',
                'qty' => 13,
                'note' => 'Initial stock',
                'before_qty' => 0,
                'after_qty' => 13,
            ]);

            StockTransaction::create([
                'product_id' => $product->id,
                'type' => 'OUT',
                'qty' => 10,
                'note' => 'First sale',
                'before_qty' => 13,
                'after_qty' => 3,
            ]);
        }
    }
}
