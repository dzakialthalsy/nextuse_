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
                @if(session('success'))
                    <div class="mb-6 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-900">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->has('permohonan'))
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first('permohonan') }}
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            {{ ($item->status ?? 'tersedia') === 'habis' ? 'Sudah dihibahkan' : 'Tersedia' }}
                        </span>
                        <span class="text-sm text-gray-500">
                            Diunggah {{ $item->created_at->diffForHumans() }}
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

                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $item->deskripsi }}
                    </p>

                    <div class="mt-6 border-t border-gray-100 pt-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold">
                                {{ strtoupper(substr($item->organization->organization_name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Dibagikan oleh</p>
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

                    @php $hasGiveaway = is_array($item->preferensi) && in_array('giveaway', $item->preferensi); @endphp
                    @if($hasGiveaway)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Preferensi</p>
                            <div class="flex flex-wrap gap-2">
                                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">Berbagi Barang</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($item->catatan_pengambilan))
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Catatan Pengambilan</p>
                            <p class="text-gray-700">{{ $item->catatan_pengambilan }}</p>
                        </div>
                    @endif

                    @if(!empty($item->applicant_requirements))
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Syarat Ketentuan Pemohon</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $item->applicant_requirements }}</p>
                        </div>
                    @endif
                </div>

                @php
                    $isLoggedIn = session()->has('organization_id');
                    $isReceiver = session('is_receiver') === true;
                    $isOwner = $isLoggedIn && (int) session('organization_id') === (int) $item->organization_id;
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Ajukan Permohonan Barang</h2>
                    @if($isLoggedIn && $isReceiver && !$isOwner)
                        <p class="text-sm text-gray-600">Lengkapi surat kuasa sesuai template resmi, lalu ajukan permohonan Anda kepada pemilik barang.</p>
                        <a href="{{ route('item-requests.create', $item->id) }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-teal-600 text-white font-semibold rounded-xl hover:bg-teal-700 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8m-4-4v8" />
                            </svg>
                            Ajukan Permohonan
                        </a>
                    @elseif(!$isLoggedIn)
                        <p class="text-sm text-gray-600">Masuk sebagai organisasi penerima untuk mengajukan permohonan.</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-teal-200 text-teal-700 font-semibold rounded-xl hover:bg-teal-50 transition shadow-sm">
                            Masuk untuk Mengajukan
                        </a>
                    @elseif($isOwner)
                        <p class="text-sm text-gray-600">Ini adalah barang yang Anda bagikan. Permohonan tidak diperlukan.</p>
                    @else
                        <p class="text-sm text-gray-600">Fitur permohonan hanya tersedia bagi organisasi penerima terverifikasi.</p>
                    @endif
                </div>

                @php $isDonor = session('is_donor') === true; @endphp
                @if($isDonor)
                    <div class="flex gap-4">
                        <a href="{{ route('post-item.create') }}"
                           class="inline-flex items-center justify-center px-6 py-4 bg-teal-600 text-white font-semibold rounded-xl hover:bg-teal-700 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Unggah Barang Lain
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
