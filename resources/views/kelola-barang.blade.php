@extends('layouts.app')

@section('title', 'Inventori Saya - NextUse')

@section('content')
<style>
    /* Override layout background untuk halaman ini */
    body {
        background-color: #ffffff !important;
    }
    
    /* Adjust main padding untuk halaman ini */
    main {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    
    /* Container utama untuk membatasi lebar konten */
    .main-container {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px 32px;
        background-color: #ffffff;
    }

    /* FILTER AND SEARCH BAR LAYOUT */
    .search-input-wrapper {
        flex-grow: 1;
    }
    .filter-buttons {
        display: flex;
        gap: 8px;
    }
    .filter-element {
        height: 38px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background-color: white;
        display: flex;
        align-items: center;
        padding: 0 10px; 
        font-size: 14px;
        color: #4b5563;
        cursor: pointer;
        position: relative;
    }
    .filter-element:hover {
        border-color: #9ca3af;
    }
    .filter-element select {
        border: none;
        outline: none;
        padding: 0;
        background-color: transparent;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .filter-element .arrow-icon {
        margin-left: 4px;
        color: #9ca3af;
    }
    .filter-element .sort-icon {
        margin-right: 4px;
        color: #9ca3af;
    }

    /* BADGE AND ICON BOX COLORS */
    .badge {
        padding: 3px 10px; 
        border-radius: 9999px;
        font-size: 12px; 
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
    }
    .badge-dapur { background-color: #e5f5f7; color: #007c91; }
    .badge-olahraga { background-color: #f9e2e7; color: #e54d72; }
    .badge-buku { background-color: #e3f9ed; color: #15803d; }
    .badge-perabotan { background-color: #f9f5e7; color: #a16207; }
    .badge-elektronik { background-color: #e6f1fb; color: #1d4ed8; }

    /* STATUS COLORS */
    .status-tersedia { background-color: #ecfdf5; color: #059669; }
    .status-habis { background-color: #fee2e2; color: #ef4444; }
    .status-reserved { background-color: #fffbeb; color: #f59e0b; }
    
    .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-box-olahraga { background-color: #fef0f7; color: #f43f80; }
    .icon-box-buku { background-color: #e9f7f4; color: #0f766e; }
    .icon-box-perabotan { background-color: #fffaf0; color: #92400e; }
    .icon-box-elektronik { background-color: #eff6ff; color: #2563eb; }
    .icon-box-dapur { background-color: #ecfdf5; color: #059669; }
</style>

<div class="main-container w-full">
    {{-- Header Section --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Inventori Saya</h1>
                <p class="text-gray-500 mt-2 text-base">Kelola semua barang yang Anda posting.</p>
            </div>
            
            <button class="flex items-center space-x-2 px-4 py-2 main-gradient hover:opacity-90 text-white rounded-lg font-medium transition-colors shadow-md text-sm">
                {{-- Plus Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Posting Barang Baru</span>
            </button>
        </div>

        {{-- Search and Filter Section --}}
        <div class="flex items-center space-x-3 mb-6">
            {{-- Search Input --}}
            <div class="search-input-wrapper relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" placeholder="Cari barang..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-gray-300 focus:border-gray-300 text-sm placeholder-gray-400 h-10" />
            </div>

            {{-- Filter Group --}}
            <div class="filter-buttons">
                {{-- Filter Icon Button --}}
                <div class="filter-element w-10 h-10 p-0 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a2.25 2.25 0 1 1-4.5 0m4.5 0a2.25 2.25 0 1 0-4.5 0M18.75 12h.008v.008h-.008V12Zm-12 0h.008v.008h-.008V12Zm4.5 0h.008v.008h-.008V12Zm-12 0a2.25 2.25 0 1 0 0 4.5h15.75a2.25 2.25 0 1 0 0-4.5H3.75Z" />
                    </svg>
                </div>

                {{-- Dropdown 1: Semua Kategori --}}
                <div class="filter-dropdown-wrapper filter-element">
                    <select class="pl-0 pr-5 text-gray-700">
                        <option>Semua Kategori</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="arrow-icon w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
                
                {{-- Dropdown 2: Semua Status --}}
                <div class="filter-dropdown-wrapper filter-element">
                    <select class="pl-0 pr-5 text-gray-700">
                        <option>Semua Status</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="arrow-icon w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>

                {{-- Dropdown 3: Terbaru (Sort) --}}
                <div class="filter-dropdown-wrapper filter-element">
                    {{-- Sort Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sort-icon w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                    </svg>
                    <select class="pl-0 pr-5 text-gray-700">
                        <option>Terbaru</option>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="arrow-icon w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm bg-white">
            <table class="min-w-full divide-y divide-gray-100">
                {{-- Table Header --}}
                <thead class="bg-gray-50 text-gray-500 font-medium text-sm">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left w-1/12">
                            <input type="checkbox" class="rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left w-1/12">Foto</th>
                        <th scope="col" class="px-6 py-3 text-left w-4/12">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left w-2/12">Kategori</th>
                        <th scope="col" class="px-6 py-3 text-left w-2/12">Status</th>
                        <th scope="col" class="px-6 py-3 text-right w-1/12">Aksi</th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="divide-y divide-gray-100 text-gray-900 text-sm">
                    @foreach ($items as $item)
                        @php
                            $category_class = match ($item['kategori']) {
                                'Dapur' => 'badge-dapur',
                                'Olahraga' => 'badge-olahraga',
                                'Buku & Alat Tulis' => 'badge-buku',
                                'Perabotan' => 'badge-perabotan',
                                'Elektronik' => 'badge-elektronik',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $status_class = match ($item['status']) {
                                'Tersedia' => 'status-tersedia',
                                'Habis' => 'status-habis',
                                'Reserved' => 'status-reserved',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $icon_box_class = match ($item['kategori']) {
                                'Dapur' => 'icon-box-dapur',
                                'Olahraga' => 'icon-box-olahraga',
                                'Buku & Alat Tulis' => 'icon-box-buku',
                                'Perabotan' => 'icon-box-perabotan',
                                'Elektronik' => 'icon-box-elektronik',
                                default => 'bg-gray-100 text-gray-500',
                            };
                        @endphp

                        <tr class="hover:bg-gray-50">
                            {{-- Checkbox --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-green-500 shadow-sm focus:ring-green-500">
                            </td>

                            {{-- Foto Icon Box --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="icon-box {{ $icon_box_class }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                        <path d="M7.494 2.378A.75.75 0 0 1 8.25 3v1.5H15V3a.75.75 0 0 1 .756-.622L20.5 4.5l-2.072 2.392L18 8.169V20.25a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V8.169l-.428-1.277L3.5 4.5l5.072-2.122Z" />
                                        <path fill-rule="evenodd" d="M11.25 6.75a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V7.5a.75.75 0 0 1 .75-.75Zm3.75 0a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V7.5a.75.75 0 0 1 .75-.75Zm-7.5 0a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V7.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </td>

                            {{-- Judul --}}
                            <td class="px-6 py-3 whitespace-nowrap font-medium">
                                {{ $item['judul'] }}
                            </td>

                            {{-- Kategori Badge --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <span class="badge {{ $category_class }}">
                                    {{ $item['kategori'] }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <span class="badge {{ $status_class }}">
                                    {{ $item['status'] }}
                                </span>
                            </td>

                            {{-- Aksi Menu --}}
                            <td class="px-6 py-3 whitespace-nowrap text-right">
                                <a href="#" class="inline-block text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-auto">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
</div>
@endsection

