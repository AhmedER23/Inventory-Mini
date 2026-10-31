<x-app-layout>
    <x-breadcrumbs :items="[
        ['label' => __('ui.Dashboard'), 'url' => route('dashboard')],
        ['label' => __('ui.Products'), 'url' => route('products.index')],
        ['label' => $product->name],
    ]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-sub">{{ __('ui.SKU') }}: {{ $product->sku }} &bull; {{ $product->localized_unit }}</p>
        </div>
        <div class="d-flex align-items-center" style="gap: 8px;">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="printBarcodeLabel()">
                {{ __('ui.Print Barcode Label') }}
            </button>
            @if ($product->is_low_stock)
                <span class="badge-soft warn">{{ __('ui.Low Stock') }}</span>
            @else
                <span class="badge-soft ok">{{ __('ui.Normal') }}</span>
            @endif
        </div>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <section class="panel mb-4">
                <h5 class="mb-3">{{ __('ui.Product') }}</h5>
                <dl class="def-list">
                    <dt>{{ __('ui.SKU') }}</dt>
                    <dd class="fw-bold font-monospace">{{ $product->sku }}</dd>
                    <dt>{{ __('ui.Product Name') }}</dt>
                    <dd>{{ $product->name }}</dd>
                    <dt>{{ __('ui.Quantity') }}</dt>
                    <dd><strong>{{ number_format($product->quantity) }}</strong> {{ $product->localized_unit }}</dd>
                    <dt>{{ __('ui.Unit') }}</dt>
                    <dd>{{ $product->localized_unit }}</dd>
                    <dt>{{ __('ui.Low Stock Threshold') }}</dt>
                    <dd>{{ number_format($product->low_stock_threshold) }} {{ $product->localized_unit }}</dd>
                    <dt>{{ __('ui.Description') }}</dt>
                    <dd>{{ $product->description ?: '-' }}</dd>
                </dl>
            </section>

            <section class="panel text-center">
                <h6 class="mb-3 text-start">{{ __('ui.Barcode & Label') }}</h6>
                <div class="p-3 bg-white border rounded mb-3 d-inline-block">
                    <svg id="barcode"></svg>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="printBarcodeLabel()">
                        {{ __('ui.Print Barcode Label') }}
                    </button>
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            <div class="table-wrap h-100">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <strong>{{ __('ui.Transaction History') }}</strong>
                    <span class="text-muted" style="font-size: 0.88rem;">{{ __('ui.Total Transactions') }}: {{ $transactions->total() }}</span>
                </div>
                <div class="table-responsive">
                    <table id="txTable" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('ui.Date & Time') }}</th>
                                <th>{{ __('ui.Type') }}</th>
                                <th>{{ __('ui.Quantity') }}</th>
                                <th>{{ __('ui.Before') }}</th>
                                <th>{{ __('ui.After') }}</th>
                                <th>{{ __('ui.Note') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $t)
                                <tr>
                                    <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                                    <td><span class="badge-soft {{ $t->type === 'IN' ? 'ok' : 'danger' }}">{{ $t->type === 'IN' ? __('ui.Stock In') : __('ui.Stock Out') }}</span></td>
                                    <td>{{ number_format($t->qty) }} {{ $product->localized_unit }}</td>
                                    <td>{{ number_format($t->before_qty) }}</td>
                                    <td>{{ number_format($t->after_qty) }}</td>
                                    <td>{{ $t->localized_note }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="p-3 border-top">{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div id="printLabelArea" class="d-none">
        <div style="width: 320px; padding: 16px; border: 2px dashed #000; text-align: center; font-family: sans-serif; margin: 0 auto;">
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 4px;">Inventory Mini</div>
            <div style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">{{ $product->name }}</div>
            <div style="margin-bottom: 6px;"><svg id="printBarcodeSvg"></svg></div>
            <div style="font-size: 12px; color: #555;">{{ __('ui.Current Stock') }}: {{ number_format($product->quantity) }} {{ $product->localized_unit }}</div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        <script>
            $(function () {
                try {
                    JsBarcode("#barcode", @json($product->sku), {
                        format: "CODE128",
                        lineColor: "#0f172a",
                        width: 2,
                        height: 50,
                        displayValue: true,
                        fontSize: 14
                    });
                    JsBarcode("#printBarcodeSvg", @json($product->sku), {
                        format: "CODE128",
                        lineColor: "#000",
                        width: 2,
                        height: 50,
                        displayValue: true,
                        fontSize: 14
                    });
                } catch (e) {
                    console.error("Barcode generation error", e);
                }

                $('#txTable').DataTable({
                    paging: false,
                    info: false,
                    order: [[0, 'desc']],
                    dom: 'Bfrtip',
                    language: {
                        search: @json(__('ui.Search:')),
                        zeroRecords: @json(__('ui.No matching records found')),
                        emptyTable: @json(__('ui.No data available in table')),
                    },
                    buttons: [
                        { extend: 'copy', text: @json(__('ui.Copy')) },
                        { extend: 'csv', text: 'CSV' },
                        { extend: 'excel', text: 'Excel' },
                        { extend: 'pdf', text: 'PDF' },
                        { extend: 'print', text: @json(__('ui.Print')) },
                    ],
                });
            });

            function printBarcodeLabel() {
                const content = document.getElementById('printLabelArea').innerHTML;
                const win = window.open('', '', 'height=500,width=500');
                win.document.write('<html><head><title>{{ $product->sku }} - {{ __('ui.Barcode & Label') }}</title>');
                win.document.write('<style>body { margin: 20px; font-family: sans-serif; display: flex; justify-content: center; align-items: center; }</style>');
                win.document.write('</head><body>');
                win.document.write(content);
                win.document.write('</body></html>');
                win.document.close();
                win.focus();
                setTimeout(function () {
                    win.print();
                    win.close();
                }, 400);
            }
        </script>
    @endpush
</x-app-layout>
