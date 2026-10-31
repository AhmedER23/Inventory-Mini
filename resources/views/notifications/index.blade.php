@php
    $canManage = auth()->user()->canManageInventory();
@endphp
<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard'), 'url' => route('dashboard')], ['label' => __('ui.Notifications')]]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.Notifications & Alerts') }}</h1>
            <p class="page-sub">{{ __('ui.System alerts, low stock warnings, and inventory notifications') }}</p>
        </div>
    </section>

    @if ($lowStockProducts->isEmpty())
        <section class="panel text-center py-5">
            <h4 class="text-success">{{ __('ui.All Products Are Well Stocked') }}</h4>
            <p class="text-muted">{{ __('ui.No products currently need restocking. Your inventory levels are healthy.') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">{{ __('ui.View All Products') }}</a>
        </section>
    @else
        <div class="alert alert-warning">
            {{ __('ui.You have') }} {{ $lowStockProducts->count() }} {{ __('ui.product(s) that need immediate restocking attention.') }}
        </div>

        <div class="row g-4">
            @foreach ($lowStockProducts as $product)
                <div class="col-lg-4">
                    <article class="panel h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $product->name }}</h5>
                                <p class="text-muted mb-0">{{ $product->sku }}</p>
                            </div>
                            <span class="badge-soft warn">{{ __('ui.Low Stock') }}</span>
                        </div>
                        <p class="mb-2">{{ __('ui.Current Stock') }}: <strong>{{ number_format($product->quantity) }}</strong></p>
                        <p class="mb-3">{{ __('ui.Threshold') }}: <strong>{{ number_format($product->low_stock_threshold) }}</strong></p>
                        <div class="d-flex" style="gap:8px;">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">{{ __('ui.View Details') }}</a>
                            @if ($canManage)
                                <a href="{{ route('transactions.index', ['product' => $product->id]) }}" class="btn btn-sm btn-success">{{ __('ui.Add Transaction') }}</a>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
