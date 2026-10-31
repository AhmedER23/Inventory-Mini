<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard'), 'url' => route('dashboard')], ['label' => __('ui.Settings')]]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.System Settings') }}</h1>
            <p class="page-sub">{{ __('ui.Configure application preferences and system behavior') }}</p>
        </div>
    </section>

    <section class="panel mb-4">
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 20px;">{{ __('ui.General Preferences') }}</h2>
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label" for="app_name">{{ __('ui.Application Name') }}</label>
                    <input type="text" id="app_name" name="app_name" class="form-control" maxlength="255" required
                           value="{{ old('app_name', $settings['app_name']) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="default_low_stock_threshold">{{ __('ui.Default Low Stock Threshold') }}</label>
                    <input type="number" id="default_low_stock_threshold" name="default_low_stock_threshold" class="form-control" min="0" required
                           value="{{ old('default_low_stock_threshold', $settings['default_low_stock_threshold']) }}">
                </div>
            </div>

            <div class="form-check mt-4">
                <input type="checkbox" class="form-check-input" id="cache_reports" name="cache_reports" value="1"
                       @checked(old('cache_reports', $settings['cache_reports']))>
                <label class="form-check-label" for="cache_reports">{{ __('ui.Enable Report Caching') }}</label>
            </div>

            <div class="d-flex justify-content-end mt-4" style="gap:8px;">
                <button type="submit" class="btn btn-primary">{{ __('ui.Save Settings') }}</button>
            </div>
        </form>
    </section>

    <section class="panel mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 4px;">{{ __('ui.User Roles & Permissions') }}</h2>
                <p class="text-muted mb-0" style="font-size: 0.88rem;">{{ __('ui.Manage roles for registered users (Admin, Manager, User)') }}</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>{{ __('ui.Name') }}</th>
                        <th>{{ __('ui.Email') }}</th>
                        <th>{{ __('ui.Status') }}</th>
                        <th>{{ __('ui.Current Role') }}</th>
                        <th>{{ __('ui.Change Role') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->hasVerifiedEmail())
                                    <span class="badge-soft ok">{{ __('ui.Verified') }}</span>
                                @else
                                    <span class="badge-soft warn">{{ __('ui.Unverified') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->role === 'admin')
                                    <span class="badge-soft ok">{{ __('ui.Admin') }}</span>
                                @elseif ($user->role === 'manager')
                                    <span class="badge-soft warn">{{ __('ui.Manager') }}</span>
                                @else
                                    <span class="badge-soft">{{ __('ui.User (Viewer)') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <form method="POST" action="{{ route('settings.users.role', $user) }}" class="d-flex align-items-center" style="gap: 6px;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-select form-select-sm" style="max-width: 140px;">
                                            <option value="admin" @selected($user->role === 'admin')>{{ __('ui.Admin') }}</option>
                                            <option value="manager" @selected($user->role === 'manager')>{{ __('ui.Manager') }}</option>
                                            <option value="user" @selected($user->role === 'user')>{{ __('ui.User (Viewer)') }}</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('ui.Update') }}</button>
                                    </form>

                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('settings.users.destroy', $user) }}" onsubmit="return confirm(@json(__('ui.Are you sure you want to delete this user?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('ui.Delete') }}">&times;</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel border-danger" style="border-left: 4px solid var(--danger);">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--danger); margin-bottom: 6px;">
            {{ __('ui.Danger Zone') }}
        </h2>
        <p class="text-muted" style="font-size: 0.9rem;">
            {{ __('ui.Wipe all demo/sample products and stock transactions to start with a completely empty inventory.') }}
        </p>

        <form method="POST" action="{{ route('settings.reset-inventory') }}" onsubmit="return confirm(@json(__('ui.Are you sure you want to delete all products and stock history? This cannot be undone.')))">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                {{ __('ui.Reset Inventory to Empty') }}
            </button>
        </form>
    </section>
</x-app-layout>
