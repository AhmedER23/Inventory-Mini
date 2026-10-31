<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard')]]" />

    @if ($lowStockProducts > 0)
        <div class="alert alert-warning d-flex align-items-center justify-content-between p-3 mb-4" style="border-radius: 16px; border-left: 6px solid #f59e0b; background: #fffbeb;">
            <div class="d-flex align-items-center" style="gap: 12px;">
                <div>
                    <strong style="color: #92400e; font-size: 1rem;">{{ __('ui.Low Stock Warning') }}</strong>
                    <div style="color: #b45309; font-size: 0.92rem;">
                        {{ __('ui.Attention: :count product(s) are below the low stock threshold! Immediate restocking is recommended.', ['count' => $lowStockProducts]) }}
                    </div>
                </div>
            </div>
            <a href="{{ route('notifications.index') }}" class="btn btn-warning btn-sm fw-semibold" style="white-space: nowrap;">
                {{ __('ui.View Low Stock Items') }} &rarr;
            </a>
        </div>
    @endif

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.Dashboard') }}</h1>
            <p class="page-sub">{{ __('ui.Live overview for products, stock movement and low stock alerts') }}</p>
        </div>
        @if (auth()->user()->canManageInventory())
            <div class="d-flex" style="gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn btn-success btn-sm js-quick-in">
                    {{ __('ui.Stock In') }}
                </button>
                <button type="button" class="btn btn-danger btn-sm js-quick-out">
                    {{ __('ui.Stock Out') }}
                </button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProductModal">
                    {{ __('ui.Add Product') }}
                </button>
            </div>
        @endif
    </section>

    <div class="metric-grid">
        <div class="metric">
            <span class="metric-label">{{ __('ui.Total Products') }}</span>
            <strong class="metric-value">{{ number_format($totalProducts) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Total Transactions') }}</span>
            <strong class="metric-value">{{ number_format($totalTransactions) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Low Stock Items') }}</span>
            <strong class="metric-value {{ $lowStockProducts > 0 ? 'text-danger' : '' }}">{{ number_format($lowStockProducts) }}</strong>
        </div>
    </div>

    <section class="table-wrap">
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
            <strong>{{ __('ui.Recent Transactions') }}</strong>
            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('ui.View All') }} &rarr;</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('ui.Product') }}</th>
                        <th>{{ __('ui.Type') }}</th>
                        <th>{{ __('ui.Quantity') }}</th>
                        <th>{{ __('ui.Date & Time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentTransactions as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>
                                <strong>{{ $t->product?->name ?? '-' }}</strong>
                                <small class="text-muted d-block">{{ $t->product?->sku }}</small>
                            </td>
                            <td>
                                <span class="badge-soft {{ $t->type === 'IN' ? 'ok' : 'danger' }}">
                                    {{ $t->type === 'IN' ? __('ui.Stock In') : __('ui.Stock Out') }}
                                </span>
                            </td>
                            <td>{{ number_format($t->qty) }} {{ $t->product?->localized_unit ?? __('ui.Adet') }}</td>
                            <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">{{ __('ui.No transactions yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if (auth()->user()->canManageInventory())
        <div class="modal fade" id="quickTransactionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('transactions.store') }}" class="modal-content">
                    @csrf
                    <input type="hidden" name="_form" value="add-transaction">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickTxTitle">{{ __('ui.Add Stock Transaction') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="quick_product_id">{{ __('ui.Select Product') }}</label>
                            <select id="quick_product_id" name="product_id" class="form-select" required>
                                <option value="">{{ __('ui.Choose a product...') }}</option>
                                @foreach ($allProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} ({{ $product->sku }}) - {{ number_format($product->quantity) }} {{ $product->localized_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="quick_type">{{ __('ui.Transaction Type') }}</label>
                                <select id="quick_type" name="type" class="form-select" required>
                                    <option value="IN">{{ __('ui.Stock In') }}</option>
                                    <option value="OUT">{{ __('ui.Stock Out') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="quick_qty">{{ __('ui.Quantity') }}</label>
                                <input type="number" id="quick_qty" name="qty" class="form-control" min="1" required placeholder="0">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="quick_note">{{ __('ui.Note (Optional)') }}</label>
                            <input type="text" id="quick_note" name="note" class="form-control" maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('ui.Cancel') }}</button>
                        <button type="submit" class="btn btn-success" id="quickTxSubmitBtn">{{ __('ui.Record Transaction') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('products.store') }}" class="modal-content">
                    @csrf
                    <input type="hidden" name="_form" value="create-product">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('ui.Create Product') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="create_sku">{{ __('ui.SKU') }}</label>
                            <input type="text" id="create_sku" name="sku" class="form-control" maxlength="50" required
                                   pattern="[A-Za-z0-9\-_]+" oninput="this.value = this.value.toUpperCase()"
                                   placeholder="SKU-1001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="create_name">{{ __('ui.Product Name') }}</label>
                            <input type="text" id="create_name" name="name" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="create_description">{{ __('ui.Description') }}</label>
                            <textarea id="create_description" name="description" class="form-control" rows="2" maxlength="1000"></textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <label class="form-label" for="create_quantity">{{ __('ui.Initial Quantity') }}</label>
                                <input type="number" id="create_quantity" name="quantity" class="form-control" min="0" required value="0">
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="create_unit">{{ __('ui.Unit') }}</label>
                                <select id="create_unit" name="unit" class="form-select">
                                    <option value="Adet">{{ __('ui.Adet') }}</option>
                                    <option value="Koli">{{ __('ui.Koli') }}</option>
                                    <option value="Kg">{{ __('ui.Kg') }}</option>
                                    <option value="Litre">{{ __('ui.Litre') }}</option>
                                    <option value="Paket">{{ __('ui.Paket') }}</option>
                                    <option value="Metre">{{ __('ui.Metre') }}</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="create_threshold">{{ __('ui.Low Stock Threshold') }}</label>
                                <input type="number" id="create_threshold" name="low_stock_threshold" class="form-control" min="0" value="5">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('ui.Cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('ui.Create Product') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            $(function () {
                $('.js-quick-in').on('click', function () {
                    $('#quick_type').val('IN');
                    $('#quickTxTitle').text(@json(__('ui.Stock In')));
                    $('#quickTxSubmitBtn').removeClass('btn-danger').addClass('btn-success');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('quickTransactionModal')).show();
                });

                $('.js-quick-out').on('click', function () {
                    $('#quick_type').val('OUT');
                    $('#quickTxTitle').text(@json(__('ui.Stock Out')));
                    $('#quickTxSubmitBtn').removeClass('btn-success').addClass('btn-danger');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('quickTransactionModal')).show();
                });
            });
        </script>
    @endpush
</x-app-layout>
