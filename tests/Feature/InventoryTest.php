<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_pages_render_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        foreach (['dashboard', 'products', "products/{$product->id}", 'transactions', 'reports', 'notifications', 'settings'] as $path) {
            $this->actingAs($admin)->get("/en/{$path}")->assertOk();
        }
    }

    public function test_pages_render_in_turkish_and_arabic(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (['tr', 'ar'] as $locale) {
            $this->actingAs($admin)->get("/{$locale}/dashboard")->assertOk();
        }
    }

    public function test_admin_can_create_product_with_initial_stock_history(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/en/products', [
            'sku' => 'SKU-9001',
            'name' => 'Test Product',
            'description' => 'Demo',
            'quantity' => 10,
            'low_stock_threshold' => 3,
        ]);

        $response->assertRedirect('/en/products');
        $this->assertDatabaseHas('products', ['sku' => 'SKU-9001', 'quantity' => 10]);
        $this->assertDatabaseHas('stock_transactions', ['type' => 'IN', 'qty' => 10, 'before_qty' => 0, 'after_qty' => 10]);
    }

    public function test_product_threshold_falls_back_to_setting(): void
    {
        $admin = User::factory()->admin()->create();
        Setting::set('default_low_stock_threshold', '9');

        $this->actingAs($admin)->post('/en/products', [
            'sku' => 'SKU-9002',
            'name' => 'Threshold Product',
            'quantity' => 0,
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'SKU-9002', 'low_stock_threshold' => 9]);
    }

    public function test_sku_format_is_validated(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/en/products', [
            'sku' => 'bad sku!',
            'name' => 'Bad',
            'quantity' => 1,
        ])->assertSessionHasErrors('sku');
    }

    public function test_regular_user_cannot_modify_products(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/en/products', [
            'sku' => 'SKU-9003',
            'name' => 'Nope',
            'quantity' => 1,
        ])->assertForbidden();

        $this->actingAs($user)->get('/en/settings')->assertForbidden();
    }

    public function test_stock_in_and_out_update_quantity_and_history(): void
    {
        $manager = User::factory()->manager()->create();
        $product = Product::factory()->create(['quantity' => 5]);

        $this->actingAs($manager)->post('/en/transactions', [
            'product_id' => $product->id,
            'type' => 'IN',
            'qty' => 7,
        ])->assertRedirect('/en/transactions');

        $this->assertSame(12, $product->fresh()->quantity);

        $this->actingAs($manager)->post('/en/transactions', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'qty' => 2,
            'note' => 'sale',
        ]);

        $this->assertSame(10, $product->fresh()->quantity);
        $this->assertDatabaseHas('stock_transactions', ['product_id' => $product->id, 'type' => 'OUT', 'before_qty' => 12, 'after_qty' => 10]);
    }

    public function test_cannot_withdraw_more_than_available_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 3]);

        $response = $this->actingAs($admin)->post('/en/transactions', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'qty' => 4,
        ]);

        $response->assertRedirect('/en/transactions');
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors('qty');

        $this->assertSame(3, $product->fresh()->quantity);
        $this->assertSame(0, StockTransaction::count());
    }

    public function test_manual_quantity_edit_writes_adjustment_transaction(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 5, 'low_stock_threshold' => 2]);

        $this->actingAs($admin)->put("/en/products/{$product->id}", [
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => null,
            'quantity' => 8,
            'low_stock_threshold' => 2,
        ])->assertRedirect('/en/products');

        $this->assertDatabaseHas('stock_transactions', ['product_id' => $product->id, 'type' => 'IN', 'qty' => 3, 'note' => 'Manual adjustment']);
    }

    public function test_product_search_filters_results(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->create(['sku' => 'SKU-1111', 'name' => 'Alpha Widget']);
        Product::factory()->create(['sku' => 'SKU-2222', 'name' => 'Beta Gadget']);

        $this->actingAs($admin)->get('/en/products?search=Alpha')
            ->assertOk()
            ->assertSee('Alpha Widget')
            ->assertDontSee('Beta Gadget');
    }

    public function test_settings_can_be_updated_by_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/en/settings', [
            'app_name' => 'My Inventory',
            'default_low_stock_threshold' => 7,
            'cache_reports' => 1,
        ])->assertRedirect('/en/settings');

        $this->assertSame('My Inventory', Setting::get('app_name'));
        $this->assertSame('7', Setting::get('default_low_stock_threshold'));
    }

    public function test_reports_show_period_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['quantity' => 20]);

        $this->actingAs($admin)->post('/en/transactions', ['product_id' => $product->id, 'type' => 'IN', 'qty' => 5]);
        $this->actingAs($admin)->post('/en/transactions', ['product_id' => $product->id, 'type' => 'OUT', 'qty' => 2]);

        $this->actingAs($admin)->get('/en/reports?days=7')->assertOk()->assertSee($product->name);
    }
}
