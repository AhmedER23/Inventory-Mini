<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => Setting::get('app_name', config('app.name')),
            'default_low_stock_threshold' => Setting::get('default_low_stock_threshold', 5),
            'cache_reports' => filter_var(Setting::get('cache_reports', '1'), FILTER_VALIDATE_BOOLEAN),
        ];

        $users = User::latest()->get();

        return view('settings.index', compact('settings', 'users'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'default_low_stock_threshold' => ['required', 'integer', 'min:0'],
            'cache_reports' => ['nullable', 'boolean'],
        ]);

        Setting::set('app_name', $data['app_name']);
        Setting::set('default_low_stock_threshold', (string) $data['default_low_stock_threshold']);
        Setting::set('cache_reports', $request->boolean('cache_reports') ? '1' : '0');

        ReportController::forgetCache();

        return redirect()->route('settings.index')
            ->with('success', __('ui.Settings saved successfully.'));
    }

    public function updateUserRole(Request $request, string $locale, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:admin,manager,user'],
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', __('ui.You cannot remove your own admin role.'));
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', __('ui.User role updated successfully.'));
    }

    public function deleteUser(Request $request, string $locale, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('ui.You cannot delete your own account here.'));
        }

        $user->delete();

        return back()->with('success', __('ui.User deleted successfully.'));
    }

    public function resetInventory(Request $request)
    {
        StockTransaction::query()->delete();
        Product::query()->delete();
        ReportController::forgetCache();

        return redirect()->route('settings.index')
            ->with('success', __('ui.All products and inventory data have been reset to empty.'));
    }
}
