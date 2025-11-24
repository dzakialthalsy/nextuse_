@extends('layouts.app')

@section('title', $item->judul . ' - Detail Barang')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- Gallery --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                @php
                    $photos = $item->foto_barang ?? [];
                @endphp
                <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 mb-4">
                    @if(!empty($photos))
                        <img src="{{ asset('storage/' . $photos[0]) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('images/product-placeholder.jpg') }}" alt="{{ $item->judul }}" class="w-full h-full object-cover opacity-80">
                    @endif
                </div>

                @if(count($photos) > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($photos as $photo)
                            <div class="h-20 rounded-xl overflow-hidden border border-gray-100">
                                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            {{ ucfirst($item->status ?? 'tersedia') }}
                        </span>
                        <span class="text-sm text-gray-500">
                            Diposting {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $item->judul }}</h1>

                    <div class="flex flex-wrap gap-3 text-sm text-gray-600 mb-4">
                        <span class="px-3 py-1 rounded-lg bg-gray-100 font-semibold">{{ $item->kategori }}</span>
                        <span class="px-3 py-1 rounded-lg border border-gray-200 font-semibold capitalize">{{ $item->kondisi }}</span>
                        <span class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $item->lokasi }}</span>
                        </span>
                    </div>

                    <p class="text-gray-700 leading-relaxed">
                        {{ $item->deskripsi }}
                    </p>

                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold">
                                {{ strtoupper(substr($item->organization->organization_name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Dibagikan oleh</p>
                                <a href="{{ route('seller.profile.show', ['organization' => $item->organization_id]) }}"
                                <a href="{{ route('profile.public', $item->organization) }}"
                                   class="text-lg font-semibold text-gray-900 hover:text-teal-600 transition">
                                    {{ $item->organization->profile->full_name ?? $item->organization->organization_name ?? 'Pengguna NextUse' }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $item->organization->organization_type ?? 'Organisasi' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h2>

                    @if(!empty($item->preferensi))
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Preferensi</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->preferensi as $pref)
                                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">{{ ucfirst($pref) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($item->catatan_pengambilan))
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Catatan Pengambilan</p>
                            <p class="text-gray-700">{{ $item->catatan_pengambilan }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 inline-flex items-center justify-center px-6 py-4 border border-gray-200 text-gray-500 font-semibold rounded-xl bg-gray-50 shadow-sm">
                        Fitur chat belum tersedia
                    </div>
                    <a href="{{ route('post-item.create') }}"
                       class="inline-flex items-center justify-center px-6 py-4 bg-teal-600 text-white font-semibold rounded-xl hover:bg-teal-700 transition shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Posting Barang Lain
                    </a>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-base font-semibold text-red-900">Laporkan Barang Ini</h3>
                        <p class="text-sm text-red-700 mt-1">Jika barang ini melanggar kebijakan NextUse atau mencurigakan, segera laporkan agar tim kami dapat meninjaunya.</p>
                    </div>
                    <a href="{{ route('report-item.create', ['item_id' => $item->id]) }}"
                       class="inline-flex items-center justify-center px-5 py-3 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 5c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                        </svg>
                        Laporkan Barang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

