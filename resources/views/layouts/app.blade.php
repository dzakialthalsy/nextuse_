<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NextUse | Jual Beli Barang Bekas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- 1. Sertakan (Include) Navbar --}}
    @include('components.navbar')

    {{-- 2. Placeholder untuk Konten Halaman --}}
    <main class="py-4 flex-1">
        @yield('content')
    </main>

    {{-- 3. Sertakan (Include) Footer --}}
    @include('components.footer')

    {{-- Tempat untuk menautkan JavaScript global --}}
    <script src="{{ asset('../../js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>