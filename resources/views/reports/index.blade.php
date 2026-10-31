<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard'), 'url' => route('dashboard')], ['label' => __('ui.Reports')]]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.Inventory Reports') }}</h1>
            <p class="page-sub">{{ __('ui.Analytics and insights for inventory management and stock levels') }}</p>
        </div>
        <form method="GET" action="{{ route('reports.index') }}" class="d-flex" style="gap:8px;">
            <select name="days" class="form-select">
                @foreach (\App\Http\Controllers\ReportController::PERIODS as $period)
                    <option value="{{ $period }}" @selected($days === $period)>{{ __('ui.Last :days days', ['days' => $period]) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">{{ __('ui.Apply') }}</button>
        </form>
    </section>

    <div class="metric-grid">
        <div class="metric">
            <span class="metric-label">{{ __('ui.Low Stock Items') }}</span>
            <strong class="metric-value">{{ number_format($lowStock->count()) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Total Products') }}</span>
            <strong class="metric-value">{{ number_format($totalProducts) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Stock In (Period)') }}</span>
            <strong class="metric-value">{{ number_format($periodStats->in_qty ?? 0) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Stock Out (Period)') }}</span>
            <strong class="metric-value">{{ number_format($periodStats->out_qty ?? 0) }}</strong>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="table-wrap h-100">
                <div class="table-head"><strong>{{ __('ui.Top Moving Products') }}</strong></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('ui.Product') }}</th>
                                <th>{{ __('ui.Transactions') }}</th>
                                <th>{{ __('ui.Total Qty') }}</th>
                                <th>{{ __('ui.Stock In') }}</th>
                                <th>{{ __('ui.Stock Out') }}</th>
                                <th>{{ __('ui.Available') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topMoving as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $row->product?->name ?? '-' }}
                                        <small class="text-muted">{{ $row->product?->sku }}</small>
                                    </td>
                                    <td>{{ number_format($row->tx_count) }}</td>
                                    <td>{{ number_format($row->total_qty) }}</td>
                                    <td>{{ number_format($row->in_qty) }}</td>
                                    <td>{{ number_format($row->out_qty) }}</td>
                                    <td>{{ number_format($row->product?->quantity ?? 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">{{ __('ui.No data available for the selected period') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="table-wrap h-100">
                <div class="table-head"><strong>{{ __('ui.Low Stock Alert') }}</strong></div>
                <div class="table-responsive">
                    <table id="lowStockTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('ui.Product') }}</th>
                                <th>{{ __('ui.Current Stock') }}</th>
                                <th>{{ __('ui.Threshold') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStock as $product)
                                <tr>
                                    <td>
                                        {{ $product->name }}
                                        <small class="text-muted">{{ $product->sku }}</small>
                                    </td>
                                    <td><span class="badge-soft danger">{{ number_format($product->quantity) }}</span></td>
                                    <td>{{ number_format($product->low_stock_threshold) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">{{ __('ui.All Products Are Well Stocked') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            $(function () {
                if ($('#lowStockTable tbody td[colspan]').length === 0) {
                    $('#lowStockTable').DataTable({
                        pageLength: 10,
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
            });
        </script>
    @endpush
</x-app-layout>
