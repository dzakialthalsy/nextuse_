@extends('layouts.app')

@php
    $products = $products ?? [];
    $categories = $categories ?? [];
@endphp

@section('title', 'Beranda Utama')

@section('content')

    <div class="hero-bg py-12 px-6 sm:px-8 lg:px-12">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6 text-center">
                <h1 class="text-xl font-semibold mb-2 text-gradient inline-block">Temukan Barang yang Kamu Butuhkan</h1>
                <p class="text-sm text-gray-500">Berbagi dan barter barang secara gratis dengan komunitas NextUse</p>
            </div>
            
            <div class="relative max-w-xl mx-auto mb-6">
                <input type="text" id="searchInput" placeholder="Cari barang yang kamu butuhkan..." value="{{ $searchQuery ?? '' }}"
                       class="w-full pl-12 pr-4 py-3 bg-gray-100 border-2 border-gray-200 shadow-md rounded-full focus:outline-none focus:ring-2 focus:ring-teal-500 placeholder-gray-500 text-gray-900 text-sm">
                <svg class="w-5 h-5 text-gray-500 absolute left-4 top-1/2 transform -translate-y-1/2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.67"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>

            <div id="categoryContainer" class="flex flex-wrap justify-center gap-2 mb-4 text-sm font-normal">
                @foreach ($categories as $cat)
                    @php
                        $slug = strtolower(str_replace(' ', '', $cat));
                        $normalizedSelected = isset($selectedCategory) && $selectedCategory !== '' ? strtolower(str_replace(' ', '', $selectedCategory)) : null;
                        $isActive = ($normalizedSelected === null && strtolower($cat) === 'semua') || ($normalizedSelected === $slug);
                    @endphp
                    <button class="category-filter px-4 py-1.5 rounded-full border border-gray-200 transition duration-150 text-xs sm:text-sm
                        {{ $isActive ? 'main-gradient text-white shadow-md' : 'bg-white text-gray-900 hover:bg-gray-50' }}" 
                        data-category="{{ $slug }}">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="sticky top-16 z-40 bg-white/95 backdrop-blur-sm border-b border-gray-100 -mx-4 sm:-mx-6 lg:-mx-8">
            <div class="px-4 sm:px-6 lg:px-8 pt-4 pb-2 flex flex-wrap gap-4 justify-between items-center text-sm">
                <span class="px-3 py-1 bg-gray-100 text-xs text-gray-700 font-medium rounded-lg">
                    <span id="productCount">{{ count($products) }}</span> barang ditemukan
                </span>
                
                <div class="flex space-x-3">
                    @php
                        $normalizedCondition = isset($selectedCondition) ? strtolower($selectedCondition) : null;
                    @endphp
                    <select id="conditionFilter" class="px-3 py-2 bg-gray-100 text-gray-900 text-sm rounded-lg border-none focus:ring-teal-500 focus:border-teal-500">
                        <option value="all" {{ $normalizedCondition === null || $normalizedCondition === 'all' ? 'selected' : '' }}>Semua Kondisi</option>
                        <option value="baru" {{ $normalizedCondition === 'baru' ? 'selected' : '' }}>Baru</option>
                        <option value="bekas" {{ $normalizedCondition === 'bekas' ? 'selected' : '' }}>Terpakai</option>
                    </select>
                    <select id="sortFilter" class="px-3 py-2 bg-gray-100 text-gray-900 text-sm rounded-lg border-none focus:ring-teal-500 focus:border-teal-500">
                        <option value="terbaru">Terbaru</option>
                        <option value="minggu ini">Minggu Ini</option>
                        <option value="bulan ini">Bulan Ini</option>
                        <option value="tahun ini">Tahun Ini</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="productList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
            @forelse ($products as $product)
                <a href="{{ route('items.show', $product['id']) }}"
                   class="block product-item bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-2xl transition duration-300"
                   data-condition="{{ $product['condition'] }}"
                   data-category="{{ $product['category'] }}"
                   data-price="{{ $product['price'] }}"
                   data-time="{{ now()->timestamp }}">
                    
                    <div class="relative {{ $product['img_class'] ?? 'h-56' }} product-image-bg flex items-center justify-center overflow-hidden">
                        @if(!empty($product['foto_barang']) && is_array($product['foto_barang']) && count($product['foto_barang']) > 0)
                            <img src="{{ asset('storage/' . $product['foto_barang'][0]) }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/product-placeholder.jpg') }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover opacity-70">
                        @endif
                        
                        <span class="absolute top-4 right-4 text-xs font-normal px-3 py-1 rounded-lg bg-green-100 text-green-800">
                            {{ $product['status'] ?? 'Tersedia' }}
                        </span>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">{{ $product['name'] }}</h3>
                        
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-xs px-2 py-1 rounded-lg bg-gray-100 text-gray-800 font-normal">{{ $product['category_display'] ?? ucfirst($product['category']) }}</span>
                            <span class="text-xs px-2 py-1 rounded-lg border border-gray-300 text-gray-900 font-normal">{{ ucfirst($product['condition']) }}</span>
                        </div>

                        <div class="border-t border-gray-100 pt-3 mb-3">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <p class="font-normal text-gray-600">{{ $product['user'] }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-1 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.33"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <p class="font-normal">{{ $product['location'] }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Belum ada barang yang sesuai</h2>
                    <p class="text-sm text-gray-500">Coba gunakan kata kunci atau filter yang berbeda.</p>
                </div>
            @endforelse
        </div>

        @if(session('is_donor') === true)
            <a href="{{ route('post-item.create') }}" class="fixed right-6 bottom-6 flex items-center space-x-2 px-6 py-4 main-gradient text-white font-medium rounded-full shadow-2xl hover:bg-teal-700 transition duration-300 transform hover:scale-105 z-50">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                <span>Posting Barang</span>
            </a>
        @endif

    </main>

@endsection
