@php
    $canManage = auth()->user()->canManageInventory();
    $totalIn = $transactions->where('type', 'IN')->sum('qty');
    $totalOut = $transactions->where('type', 'OUT')->sum('qty');
@endphp
<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard'), 'url' => route('dashboard')], ['label' => __('ui.Transactions')]]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.Stock Transactions') }}</h1>
            <p class="page-sub">{{ __('ui.Record stock in and stock out movements') }}</p>
        </div>
        @if ($canManage)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                {{ __('ui.Add Transaction') }}
            </button>
        @endif
    </section>

    @if (! $canManage)
        <div class="alert alert-light border d-flex align-items-center mb-4" style="border-radius: 14px; gap: 12px; font-size: 0.92rem; background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <div>{{ __('ui.You are logged in with Viewer role. You can inspect products and stock levels, but adding, editing, or deleting requires Manager or Admin permissions.') }}</div>
        </div>
    @endif

    <div class="metric-grid">
        <div class="metric">
            <span class="metric-label">{{ __('ui.Total Transactions') }}</span>
            <strong class="metric-value">{{ number_format($transactions->count()) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Total Stock In') }}</span>
            <strong class="metric-value">{{ number_format($totalIn) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Total Stock Out') }}</span>
            <strong class="metric-value">{{ number_format($totalOut) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Quantity') }}</span>
            <strong class="metric-value">{{ number_format($transactions->sum('qty')) }}</strong>
        </div>
    </div>

    <section class="table-wrap">
        <div class="table-responsive">
            <table id="transactionsTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('ui.Date & Time') }}</th>
                        <th>{{ __('ui.Product') }}</th>
                        <th>{{ __('ui.Type') }}</th>
                        <th>{{ __('ui.Quantity') }}</th>
                        <th>{{ __('ui.Before') }}</th>
                        <th>{{ __('ui.After') }}</th>
                        <th>{{ __('ui.Note') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if ($t->product)
                                    <a href="{{ route('products.show', $t->product) }}">{{ $t->product->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge-soft {{ $t->type === 'IN' ? 'ok' : 'danger' }}">
                                    {{ $t->type === 'IN' ? __('ui.Stock In') : __('ui.Stock Out') }}
                                </span>
                            </td>
                            <td>{{ number_format($t->qty) }} {{ $t->product?->localized_unit ?? '' }}</td>
                            <td>{{ number_format($t->before_qty) }}</td>
                            <td>{{ number_format($t->after_qty) }}</td>
                            <td>{{ $t->localized_note }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('ui.No transactions yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($canManage)
        <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('transactions.store') }}" class="modal-content">
                    @csrf
                    <input type="hidden" name="_form" value="add-transaction">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('ui.Add Stock Transaction') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="product_id">{{ __('ui.Select Product') }}</label>
                            <select id="product_id" name="product_id" class="form-select" required>
                                <option value="">{{ __('ui.Choose a product...') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected((string) old('product_id') === (string) $product->id)>
                                        {{ $product->name }} ({{ $product->sku }}) - {{ number_format($product->quantity) }} {{ $product->localized_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="type">{{ __('ui.Transaction Type') }}</label>
                                <select id="type" name="type" class="form-select" required>
                                    <option value="IN" @selected(old('type', 'IN') === 'IN')>{{ __('ui.Stock In') }}</option>
                                    <option value="OUT" @selected(old('type') === 'OUT')>{{ __('ui.Stock Out') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="qty">{{ __('ui.Quantity') }}</label>
                                <input type="number" id="qty" name="qty" class="form-control" min="1" required value="{{ old('qty') }}">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="note">{{ __('ui.Note (Optional)') }}</label>
                            <input type="text" id="note" name="note" class="form-control" maxlength="255" value="{{ old('note') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('ui.Cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('ui.Record Transaction') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            $(function () {
                if ($('#transactionsTable tbody td[colspan]').length === 0) {
                    $('#transactionsTable').DataTable({
                        order: [[0, 'desc']],
                        dom: 'Bfrtip',
                        language: {
                            search: @json(__('ui.Search:')),
                            zeroRecords: @json(__('ui.No matching records found')),
                            emptyTable: @json(__('ui.No data available in table')),
                            info: @json(__('ui.Showing _START_ to _END_ of _TOTAL_ entries')),
                            infoEmpty: @json(__('ui.Showing 0 to 0 of 0 entries')),
                            paginate: {
                                first: @json(__('ui.First')),
                                previous: @json(__('ui.Previous')),
                                next: @json(__('ui.Next')),
                                last: @json(__('ui.Last')),
                            },
                        },
                        buttons: [
                            { extend: 'copy', text: @json(__('ui.Copy')) },
                            { extend: 'csv', text: 'CSV' },
                            { extend: 'excel', text: 'Excel' },
                            { extend: 'pdf', text: 'PDF' },
                            { extend: 'print', text: @json(__('ui.Print')) },
                        ],
                    });
                }

                const modalEl = document.getElementById('addTransactionModal');

                if (modalEl) {
                    const preselect = new URLSearchParams(window.location.search).get('product');

                    if (preselect) {
                        $('#product_id').val(preselect);
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }

                    @if ($errors->any() && old('_form') === 'add-transaction')
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    @endif
                }
            });
        </script>
    @endpush
</x-app-layout>
