<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - UniHub</title>

    {{-- Bootstrap / Tailwind or your admin CSS --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        {{-- Sidebar --}}
        @include('partials.admin.sidebar')

        {{-- Main content --}}
        <div class="admin-content">
            @yield('content')
        </div>
    </div>

    {{-- JS scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
