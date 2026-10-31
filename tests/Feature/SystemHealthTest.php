<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_pages_render_without_errors_in_all_three_locales(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'name' => 'Ürün 1',
            'sku' => 'SKU-HEALTH-01',
            'quantity' => 20,
            'unit' => 'Adet',
            'low_stock_threshold' => 5,
        ]);

        foreach (['tr', 'en', 'ar'] as $locale) {
            // Guest pages (must be unauthenticated)
            Auth::logout();
            $this->get("/{$locale}")->assertOk();
            $this->get("/{$locale}/login")->assertOk();
            $this->get("/{$locale}/register")->assertOk();
            $this->get("/{$locale}/forgot-password")->assertOk();

            // Authenticated admin pages
            $this->actingAs($admin)->get("/{$locale}/dashboard")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/products")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/products/{$product->id}")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/transactions")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/reports")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/notifications")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/settings")->assertOk();
            $this->actingAs($admin)->get("/{$locale}/profile")->assertOk();
        }
    }

    public function test_role_based_access_control(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->manager()->create();
        $regularUser = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create(['quantity' => 10]);

        // Regular user cannot access settings
        $this->actingAs($regularUser)->get('/tr/settings')->assertForbidden();

        // Manager cannot access settings
        $this->actingAs($manager)->get('/tr/settings')->assertForbidden();

        // Admin can access settings
        $this->actingAs($admin)->get('/tr/settings')->assertOk();

        // Regular user cannot create product
        $this->actingAs($regularUser)->post('/tr/products', [
            'sku' => 'SKU-BLOCKED',
            'name' => 'Blocked Product',
            'quantity' => 5,
        ])->assertForbidden();

        // Manager CAN create product
        $this->actingAs($manager)->post('/tr/products', [
            'sku' => 'SKU-MGR-OK',
            'name' => 'Manager Product',
            'quantity' => 5,
            'unit' => 'Koli',
        ])->assertRedirect('/tr/products');

        $this->assertDatabaseHas('products', ['sku' => 'SKU-MGR-OK']);
    }

    public function test_insufficient_stock_out_stays_on_same_page_with_warning(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'quantity' => 4,
            'unit' => 'Adet',
        ]);

        // Attempting to withdraw 10 when only 4 are available
        $response = $this->actingAs($admin)->from('/tr/dashboard')->post('/tr/transactions', [
            'product_id' => $product->id,
            'type' => 'OUT',
            'qty' => 10,
        ]);

        // Stays on the same page (redirects back to /tr/dashboard)
        $response->assertRedirect('/tr/dashboard');
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors('qty');

        // Quantity remains unchanged
        $this->assertSame(4, $product->fresh()->quantity);
    }

    public function test_admin_can_promote_personnel_in_settings(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create(['role' => 'user']);

        // Admin promotes staff to Depo Sorumlusu (manager)
        $response = $this->actingAs($admin)->patch("/tr/settings/users/{$staff->id}/role", [
            'role' => 'manager',
        ]);

        $response->assertRedirect();
        $this->assertSame('manager', $staff->fresh()->role);
    }

    public function test_favicon_endpoints_are_reachable(): void
    {
        $this->get('/favicon.svg')->assertOk();
        $this->get('/favicon.ico')->assertOk();
    }
}
