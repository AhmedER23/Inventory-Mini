@php
    $isRtl = app()->getLocale() === 'ar';
    $appName = \App\Models\Setting::get('app_name', config('app.name'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $appName }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap / DataTables / Toastr (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/{{ $isRtl ? 'bootstrap.rtl.min.css' : 'bootstrap.min.css' }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    <div class="app-frame">
        <aside class="app-sidebar">
            <div class="app-brand-wrap">
                <a href="{{ route('dashboard') }}" class="app-brand">
                    <span class="app-brand-badge">IM</span>
                    <span>
                        <strong>{{ $appName }}</strong>
                        <small>{{ __('ui.Inventory Management') }}</small>
                    </span>
                </a>
            </div>

            <nav class="app-nav">
                <a href="{{ route('dashboard') }}" class="app-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">{{ __('ui.Dashboard') }}</a>
                <a href="{{ route('products.index') }}" class="app-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">{{ __('ui.Products') }}</a>
                <a href="{{ route('transactions.index') }}" class="app-nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">{{ __('ui.Transactions') }}</a>
                <a href="{{ route('reports.index') }}" class="app-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">{{ __('ui.Reports') }}</a>
                <a href="{{ route('notifications.index') }}" class="app-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">{{ __('ui.Notifications') }}</a>
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('settings.index') }}" class="app-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">{{ __('ui.Settings') }}</a>
                    @endif
                @endauth
            </nav>

            @include('layouts.lang-switcher')
        </aside>

        <main class="app-main">
            @include('layouts.navigation')

            <div class="app-content">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- jQuery / Bootstrap / DataTables / Toastr / SweetAlert2 (CDN) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: '{{ $isRtl ? 'toast-top-left' : 'toast-top-right' }}',
            timeOut: 4000,
        };

        if ($.fn && $.fn.dataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
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
                    }
                }
            });
        }

        const shownToastMessages = new Set();

        @if (session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if (session('error'))
            if (!shownToastMessages.has(@json(session('error')))) {
                toastr.error(@json(session('error')));
                shownToastMessages.add(@json(session('error')));
            }
        @endif

        @if (session('warning'))
            toastr.warning(@json(session('warning')));
        @endif

        @if (session('info'))
            toastr.info(@json(session('info')));
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                if (!shownToastMessages.has(@json($error))) {
                    toastr.error(@json($error));
                    shownToastMessages.add(@json($error));
                }
            @endforeach
        @endif
    </script>

    @stack('scripts')
</body>
</html>
