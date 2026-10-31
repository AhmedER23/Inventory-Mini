@php
    $canManage = auth()->user()->canManageInventory();
    $defaultThreshold = (int) \App\Models\Setting::get('default_low_stock_threshold', 5);
    $lowOnPage = $products->filter(fn ($p) => $p->is_low_stock)->count();
@endphp
<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard'), 'url' => route('dashboard')], ['label' => __('ui.Products')]]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.Products') }}</h1>
            <p class="page-sub">{{ __('ui.Manage your inventory items, stock levels and thresholds') }}</p>
        </div>
        @if ($canManage)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProductModal">
                {{ __('ui.Add Product') }}
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
            <span class="metric-label">{{ __('ui.Total Products') }}</span>
            <strong class="metric-value">{{ number_format($products->total()) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Units on This Page') }}</span>
            <strong class="metric-value">{{ number_format($products->sum('quantity')) }}</strong>
        </div>
        <div class="metric">
            <span class="metric-label">{{ __('ui.Low Stock on This Page') }}</span>
            <strong class="metric-value">{{ number_format($lowOnPage) }}</strong>
        </div>
    </div>

    <section class="panel">
        <form method="GET" action="{{ route('products.index') }}" class="d-flex" style="gap:8px; flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="max-width:360px;"
                   placeholder="{{ __('ui.Search by name or SKU') }}">
            <button type="submit" class="btn btn-primary">{{ __('ui.Search') }}</button>
            @if (request()->filled('search'))
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('ui.Reset') }}</a>
            @endif
        </form>
    </section>

    <section class="table-wrap">
        <div class="table-responsive">
            <table id="productsTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>{{ __('ui.SKU') }}</th>
                        <th>{{ __('ui.Product Name') }}</th>
                        <th>{{ __('ui.Quantity') }}</th>
                        <th>{{ __('ui.Low Stock Threshold') }}</th>
                        <th>{{ __('ui.Status') }}</th>
                        <th>{{ __('ui.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <span><strong>{{ number_format($product->quantity) }}</strong> <small class="text-muted">{{ $product->localized_unit }}</small></span>
                                    @if ($canManage)
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success py-0 px-2 js-row-stock" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-type="IN" title="{{ __('ui.Stock In') }}">+</button>
                                            <button type="button" class="btn btn-outline-danger py-0 px-2 js-row-stock" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-type="OUT" title="{{ __('ui.Stock Out') }}">-</button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>{{ number_format($product->low_stock_threshold) }} <small class="text-muted">{{ $product->localized_unit }}</small></td>
                            <td>
                                @if ($product->is_low_stock)
                                    <span class="badge-soft warn">{{ __('ui.Low Stock') }}</span>
                                @else
                                    <span class="badge-soft ok">{{ __('ui.Normal') }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">{{ __('ui.View') }}</a>
                                @if ($canManage)
                                    <button type="button" class="btn btn-sm btn-outline-secondary js-edit-product"
                                            data-update-url="{{ route('products.update', $product) }}"
                                            data-sku="{{ $product->sku }}"
                                            data-name="{{ $product->name }}"
                                            data-description="{{ $product->description }}"
                                            data-quantity="{{ $product->quantity }}"
                                            data-unit="{{ $product->unit ?? 'Adet' }}"
                                            data-threshold="{{ $product->low_stock_threshold }}">{{ __('ui.Edit') }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-delete-product"
                                            data-delete-url="{{ route('products.destroy', $product) }}">{{ __('ui.Delete') }}</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">{{ __('ui.No products found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="p-3 border-top">{{ $products->links() }}</div>
        @endif
    </section>

    @if ($canManage)
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
                            <input type="text" id="create_sku" name="sku" class="form-control @error('sku') is-invalid @enderror" maxlength="50" required
                                   pattern="[A-Za-z0-9\-_]+" oninput="this.value = this.value.toUpperCase()"
                                   value="{{ old('_form') === 'create-product' ? old('sku') : '' }}"
                                   placeholder="SKU-1012">
                            @error('sku')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="create_name">{{ __('ui.Product Name') }}</label>
                            <input type="text" id="create_name" name="name" class="form-control @error('name') is-invalid @enderror" maxlength="100" required
                                   value="{{ old('_form') === 'create-product' ? old('name') : '' }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="create_description">{{ __('ui.Description') }}</label>
                            <textarea id="create_description" name="description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="1000">{{ old('_form') === 'create-product' ? old('description') : '' }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label" for="create_quantity">{{ __('ui.Initial Quantity') }}</label>
                                <input type="number" id="create_quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror" min="0" required
                                       value="{{ old('_form') === 'create-product' ? old('quantity', 0) : 0 }}">
                                @error('quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                <input type="number" id="create_threshold" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" min="0"
                                       value="{{ old('_form') === 'create-product' ? old('low_stock_threshold', $defaultThreshold) : $defaultThreshold }}">
                                @error('low_stock_threshold')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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

        <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="editProductForm" action="" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('ui.Edit Product') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="edit_sku">{{ __('ui.SKU') }}</label>
                            <input type="text" id="edit_sku" name="sku" class="form-control" maxlength="50" required
                                   pattern="[A-Z0-9\-]+" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="edit_name">{{ __('ui.Product Name') }}</label>
                            <input type="text" id="edit_name" name="name" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="edit_description">{{ __('ui.Description') }}</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="2" maxlength="1000"></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label" for="edit_quantity">{{ __('ui.Quantity') }}</label>
                                <input type="number" id="edit_quantity" name="quantity" class="form-control" min="0" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="edit_unit">{{ __('ui.Unit') }}</label>
                                <select id="edit_unit" name="unit" class="form-select">
                                    <option value="Adet">{{ __('ui.Adet') }}</option>
                                    <option value="Koli">{{ __('ui.Koli') }}</option>
                                    <option value="Kg">{{ __('ui.Kg') }}</option>
                                    <option value="Litre">{{ __('ui.Litre') }}</option>
                                    <option value="Paket">{{ __('ui.Paket') }}</option>
                                    <option value="Metre">{{ __('ui.Metre') }}</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label" for="edit_threshold">{{ __('ui.Low Stock Threshold') }}</label>
                                <input type="number" id="edit_threshold" name="low_stock_threshold" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('ui.Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('ui.Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="quickStockModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('transactions.store') }}" class="modal-content">
                    @csrf
                    <input type="hidden" name="_form" value="add-transaction">
                    <input type="hidden" id="modal_product_id" name="product_id" value="">
                    <input type="hidden" id="modal_type" name="type" value="IN">
                    <div class="modal-header">
                        <h6 class="modal-title" id="quickStockTitle">{{ __('ui.Stock In') }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <small class="text-muted d-block">{{ __('ui.Product') }}:</small>
                            <strong id="modal_product_name">-</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="modal_qty">{{ __('ui.Quantity') }}</label>
                            <input type="number" id="modal_qty" name="qty" class="form-control" min="1" required placeholder="1">
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="modal_note">{{ __('ui.Note (Optional)') }}</label>
                            <input type="text" id="modal_note" name="note" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ __('ui.Cancel') }}</button>
                        <button type="submit" class="btn btn-sm btn-success" id="quickStockSubmit">{{ __('ui.Apply') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <form id="deleteProductForm" method="POST" action="" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    @endif

    @push('scripts')
        <script>
            $(function () {
                const exportCols = { columns: ':not(:last-child)' };

                if ($('#productsTable tbody td[colspan]').length === 0) {
                    $('#productsTable').DataTable({
                        paging: false,
                        info: false,
                        order: [],
                        columnDefs: [{ orderable: false, targets: -1 }],
                        dom: 'Bfrtip',
                        language: {
                            search: @json(__('ui.Search:')),
                            zeroRecords: @json(__('ui.No matching records found')),
                            emptyTable: @json(__('ui.No data available in table')),
                        },
                        buttons: [
                            { extend: 'copy', text: @json(__('ui.Copy')), exportOptions: exportCols },
                            { extend: 'csv', text: 'CSV', exportOptions: exportCols },
                            { extend: 'excel', text: 'Excel', exportOptions: exportCols },
                            { extend: 'pdf', text: 'PDF', exportOptions: exportCols },
                            { extend: 'print', text: @json(__('ui.Print')), exportOptions: exportCols },
                        ],
                    });
                }

                $(document).on('click', '.js-row-stock', function () {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const type = $(this).data('type');

                    $('#modal_product_id').val(id);
                    $('#modal_product_name').text(name);
                    $('#modal_type').val(type);

                    if (type === 'IN') {
                        $('#quickStockTitle').text(@json(__('ui.Stock In')));
                        $('#quickStockSubmit').removeClass('btn-danger').addClass('btn-success');
                    } else {
                        $('#quickStockTitle').text(@json(__('ui.Stock Out')));
                        $('#quickStockSubmit').removeClass('btn-success').addClass('btn-danger');
                    }

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('quickStockModal')).show();
                });

                $(document).on('click', '.js-edit-product', function () {
                    const b = this;
                    $('#editProductForm').attr('action', b.getAttribute('data-update-url'));
                    $('#edit_sku').val(b.getAttribute('data-sku'));
                    $('#edit_name').val(b.getAttribute('data-name'));
                    $('#edit_description').val(b.getAttribute('data-description'));
                    $('#edit_quantity').val(b.getAttribute('data-quantity'));
                    $('#edit_unit').val(b.getAttribute('data-unit') || 'Adet');
                    $('#edit_threshold').val(b.getAttribute('data-threshold'));
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('editProductModal')).show();
                });

                $(document).on('click', '.js-delete-product', function () {
                    const url = this.getAttribute('data-delete-url');

                    Swal.fire({
                        title: @json(__('ui.Are you sure?')),
                        text: @json(__('ui.This product and its stock history will be deleted.')),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: @json(__('ui.Yes, delete it')),
                        cancelButtonText: @json(__('ui.Cancel')),
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            const form = document.getElementById('deleteProductForm');
                            form.setAttribute('action', url);
                            form.submit();
                        }
                    });
                });

                @if ($errors->any() && old('_form') === 'create-product' && $canManage)
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('createProductModal')).show();
                @endif
            });
        </script>
    @endpush
</x-app-layout>
