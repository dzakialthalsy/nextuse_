@extends('layouts.app')

@section('title', 'Inventori Saya')

@section('content')
@php
    $categoryOptions = $items->pluck('kategori')->filter()->unique()->sort()->values();
    $conditionLabels = ['baru' => 'Baru', 'like-new' => 'Seperti Baru', 'bekas' => 'Bekas'];
    $statusLabels = ['semua' => 'Semua Status', 'tersedia' => 'Tersedia', 'reserved' => 'Reserved', 'habis' => 'Habis'];
@endphp

<div class="hero-bg py-12 px-6 sm:px-10 border-b border-gray-100">
    <div class="max-w-4xl mx-auto text-center space-y-4">
        <p class="text-sm font-semibold tracking-wide uppercase text-teal-600">Inventori Saya</p>
        <h1 class="text-3xl font-semibold text-gray-900">Kelola Barang yang Kamu Bagikan</h1>
        <p class="text-gray-600 text-base">
            Pantau semua item yang sudah kamu posting, update status ketersediaan, dan pastikan informasi selalu relevan untuk komunitas NextUse.
        </p>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
        @if(session('success'))
            <div id="toast-success" class="fixed top-4 right-4 z-50 bg-green-600 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2" role="alert">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-success');
                    if (toast) { toast.style.opacity = '0'; toast.style.transition = 'opacity 300ms'; }
                    setTimeout(() => { if (toast) toast.remove(); }, 400);
                }, 2200);
            </script>
        @endif
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center flex-wrap gap-3">
                <span class="px-3 py-1 rounded-full bg-gray-100 text-xs font-medium text-gray-700">
                    {{ $items->total() }} barang
                </span>
                <span class="px-3 py-1 rounded-full bg-teal-50 text-xs font-medium text-teal-700">Inventori aktif</span>
            </div>
            <a href="{{ route('post-item.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full main-gradient text-white text-sm font-semibold shadow-md hover:opacity-95 transition">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Unggah Barang Baru
            </a>
        </div>

        <form method="GET" class="flex flex-col gap-4 md:flex-row md:items-center">
            <div class="relative flex-1">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama barang atau deskripsi..." class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
            </div>
            <div class="flex flex-wrap gap-3">
                <select name="kategori" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="semua">Semua Kategori</option>
                    @foreach($categoryOptions as $category)
                        <option value="{{ $category }}" {{ request('kategori') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ request('status', 'semua') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="sort" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="tanggal-desc" {{ request('sort', 'tanggal-desc') === 'tanggal-desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="tanggal-asc" {{ request('sort') === 'tanggal-asc' ? 'selected' : '' }}>Terlama</option>
                    <option value="judul-asc" {{ request('sort') === 'judul-asc' ? 'selected' : '' }}>Judul A-Z</option>
                    <option value="judul-desc" {{ request('sort') === 'judul-desc' ? 'selected' : '' }}>Judul Z-A</option>
                </select>
                <button type="submit" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Terapkan</button>
            </div>
        </form>
    </section>

    <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($items as $item)
                @php
                    $photos = $item->foto_barang;
                    if (is_string($photos)) {
                        $decoded = json_decode($photos, true);
                        $photos = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
                    }
                    $photo = is_array($photos) && count($photos) ? $photos[0] : null;
                @endphp
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition flex flex-col">
                    <div class="relative h-52 product-image-bg flex items-center justify-center overflow-hidden">
                        @if($photo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($photo, 'http') ? $photo : asset('storage/'.$photo) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center text-gray-400 text-sm">
                                <p class="font-semibold text-gray-600">Belum ada foto</p>
                                <p>Tambahkan gambar agar lebih menarik</p>
                            </div>
                        @endif
                        <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold
                            @class([
                                'bg-green-100 text-green-700' => $item->status === 'tersedia',
                                'bg-yellow-100 text-yellow-700' => $item->status === 'reserved',
                                'bg-gray-200 text-gray-600' => $item->status === 'habis',
                            ])">
                            {{ ucfirst($item->status ?? 'tersedia') }}
                        </span>
                    </div>
                    <div class="flex-1 p-5 space-y-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-400">{{ $item->kategori }}</p>
                            <h3 class="text-lg font-semibold text-gray-900 mt-1">{{ $item->judul }}</h3>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs text-gray-600">
                            @if($item->kondisi)
                                <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800">{{ $conditionLabels[$item->kondisi] ?? ucfirst($item->kondisi) }}</span>
                            @endif
                            @if(is_array($item->preferensi) && in_array('giveaway', $item->preferensi))
                                <span class="px-3 py-1 rounded-full border border-gray-200">Berbagi Barang</span>
                            @endif
                        </div>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $item->lokasi }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" /><circle cx="12" cy="12" r="9" /></svg>
                                <span>Diunggah {{ $item->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 pb-5 flex gap-2">
                        <a href="{{ route('items.edit', $item->id) }}" class="flex-1 text-center text-sm font-semibold px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                            Sunting
                        </a>
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-sm font-semibold px-4 py-2.5 rounded-xl bg-red-500 text-white hover:bg-red-600 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-dashed border-gray-200 py-16 text-center">
                    <p class="text-lg font-semibold text-gray-700 mb-2">Inventori masih kosong</p>
                    <p class="text-sm text-gray-500 mb-4">Bagikan barang pertamamu dan mulai bantu sesama.</p>
                    <a href="{{ route('post-item.create') }}" class="inline-flex items-center px-5 py-3 rounded-full main-gradient text-white font-medium shadow-md">
                        Unggah Barang Sekarang
                    </a>
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $items->withQueryString()->links() }}
        </div>
    </section>
</main>
@endsection
