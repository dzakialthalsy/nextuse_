@extends('layouts.app')

@section('title', 'Permohonan Masuk Donatur')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-6xl mx-auto space-y-8">
        <header class="flex flex-col gap-3">
            <p class="text-xs uppercase tracking-wide text-teal-600 font-semibold">Donatur · Permohonan Masuk</p>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Semua Permohonan Barang</h1>
                    <p class="text-sm text-gray-600">Pantau permohonan organisasi terhadap barang yang Anda donasikan.</p>
                </div>
                <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-teal-700 hover:text-teal-800">
                    Kelola Barang
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 4.5 21 12l-7.5 7.5m7.5-7.5H3"/>
                    </svg>
                </a>
            </div>
        </header>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $statCards = [
                    ['label' => 'Total Permohonan', 'value' => number_format($stats['total']), 'accent' => 'from-teal-500/10 via-teal-500/5 to-transparent', 'text' => 'text-gray-900'],
                    ['label' => 'Menunggu Review', 'value' => number_format($stats['pending']), 'accent' => 'from-amber-500/10 via-amber-500/5 to-transparent', 'text' => 'text-amber-700'],
                    ['label' => 'Sedang Ditinjau', 'value' => number_format($stats['review']), 'accent' => 'from-sky-500/10 via-sky-500/5 to-transparent', 'text' => 'text-sky-700'],
                    ['label' => 'Disetujui', 'value' => number_format($stats['approved']), 'accent' => 'from-emerald-500/10 via-emerald-500/5 to-transparent', 'text' => 'text-emerald-700'],
                ];
            @endphp
            @foreach ($statCards as $card)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr {{ $card['accent'] }}"></div>
                    <div class="relative">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-3 text-2xl font-semibold {{ $card['text'] }}">{{ $card['value'] }}</p>
                        @if ($card['label'] === 'Disetujui')
                            <p class="text-xs text-gray-500 mt-1">Ditolak: {{ number_format($stats['rejected']) }} permohonan</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>

        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100">
                <form action="{{ route('donatur.requests.index') }}" method="GET" class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-gray-700">Cari</span>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Nama barang, organisasi, atau pesan" class="w-full rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                                </svg>
                            </span>
                        </div>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        <select name="status" class="w-full rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-gray-700">Urutkan</span>
                        <select name="sort" class="w-full rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex flex-col gap-2">
                        <span class="text-sm font-medium text-gray-700">Barang Donasi</span>
                        <select name="item_id" class="w-full rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="">Semua Barang</option>
                            @foreach ($itemsForFilter as $itemOption)
                                <option value="{{ $itemOption->id }}" @selected((string) $filters['item_id'] === (string) $itemOption->id)>
                                    {{ $itemOption->judul }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl bg-teal-600 px-3 py-2 text-sm font-medium text-white shadow hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                            Terapkan
                        </button>
                        @if ($filters['search'] || $filters['status'] !== 'semua' || $filters['sort'] !== 'terbaru' || $filters['item_id'])
                            <a href="{{ route('donatur.requests.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($requests as $requestItem)
                    @php
                        $item = $requestItem->item;
                        $receiver = $requestItem->organization;
                        $profile = $receiver?->profile;
                        $statusStyles = [
                            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                            'review' => 'bg-sky-50 text-sky-700 border border-sky-200',
                            'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border border-rose-200',
                        ];
                        $statusLabel = [
                            'pending' => 'Menunggu Review',
                            'review' => 'Sedang Ditinjau',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ][$requestItem->status] ?? ucfirst($requestItem->status);
                    @endphp
                    <article class="p-6 flex flex-col gap-5 lg:flex-row lg:items-start">
                        <div class="flex-1 space-y-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <p class="text-sm font-medium text-gray-500">Barang</p>
                                    @if($item?->status)
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                            Status Barang: {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-semibold text-gray-900">{{ $item->judul ?? 'Barang Terhapus' }}</h2>
                                    <a href="{{ $item ? route('items.show', $item->id) : '#' }}" class="text-sm font-medium text-teal-700 hover:text-teal-800">Detail barang →</a>
                                </div>
                                <div class="text-sm text-gray-600 flex flex-wrap gap-4">
                                    <span class="inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 10.5-7.5 10.5S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        {{ $item->lokasi ?? 'Lokasi tidak tersedia' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h12L17 13M7 13H5.4" />
                                        </svg>
                                        Stok tersedia: {{ $item->jumlah ?? '0' }} unit
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Pemohon</p>
                                        <p class="text-base font-semibold text-gray-900">{{ $receiver->organization_name ?? 'Organisasi tidak diketahui' }}</p>
                                        <p class="text-sm text-gray-600">{{ $receiver->contact_person ?? $receiver->email }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$requestItem->status] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                            {{ $statusLabel }}
                                        </span>
                                        <p class="mt-2 text-xs text-gray-500">Diajukan {{ $requestItem->created_at?->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                <dl class="mt-4 grid gap-3 sm:grid-cols-3 text-sm">
                                    <div>
                                        <dt class="text-gray-500">Jumlah diminta</dt>
                                        <dd class="font-semibold text-gray-900">{{ $requestItem->requested_quantity }} unit</dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Kontak</dt>
                                        <dd class="text-gray-900">
                                            {{ $profile->contact_phone ?? $receiver->phone ?? '-' }}<br>
                                            <span class="text-gray-600">{{ $profile->contact_email ?? $receiver->email ?? '-' }}</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500">Lokasi Pemohon</dt>
                                        <dd class="text-gray-900">{{ $profile->location ?? 'Belum diisi' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            @if ($requestItem->message)
                                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Pesan Permohonan</p>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $requestItem->message }}</p>
                                </div>
                            @endif

                            @if ($requestItem->surat_kuasa_path)
                                <div class="flex flex-wrap items-center gap-3 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h10M7 11h10m-4 4h4m1 6H6a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h7.586a2 2 0 0 1 1.414.586l4.414 4.414A2 2 0 0 1 20 9.414V19a2 2 0 0 1-2 2Z" />
                                    </svg>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($requestItem->surat_kuasa_path) }}" target="_blank" class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-3 py-1 text-sm font-medium text-teal-700 hover:bg-teal-50">
                                        Lihat Surat Kuasa
                                    </a>
                                </div>
                            @endif

                            @if ($requestItem->review_notes)
                                <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-4">
                                    <p class="text-sm font-medium text-amber-800 mb-1">Catatan Review</p>
                                    <p class="text-sm text-amber-700">{{ $requestItem->review_notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="lg:w-80 rounded-2xl border border-gray-200 bg-white p-4 space-y-3">
                            @if (in_array($requestItem->status, ['pending', 'review']))
                                <p class="text-sm font-semibold text-gray-900">Tetapkan Pemohon</p>
                                <p class="text-xs text-gray-600">Saat memilih pemohon, permohonan lain untuk barang ini otomatis ditolak.</p>
                                <form action="{{ route('donatur.requests.decide', $requestItem) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <label class="flex flex-col gap-1">
                                        <span class="text-xs font-medium text-gray-600">Catatan untuk pemohon (opsional)</span>
                                        <textarea name="notes" rows="3" class="rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="Contoh: Silakan hubungi kami untuk pengambilan barang."></textarea>
                                    </label>
                                    <div class="flex flex-wrap gap-3">
                                        <button type="submit" name="decision" value="approve" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" onclick="return confirm('Tetapkan pemohon ini sebagai penerima barang?');">
                                            Pilih Pemohon Ini
                                        </button>
                                        <button type="submit" name="decision" value="reject" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2" onclick="return confirm('Tolak permohonan ini?');">
                                            Tolak Permohonan
                                        </button>
                                    </div>
                                </form>
                            @elseif ($requestItem->status === 'approved')
                                <p class="text-sm font-semibold text-emerald-800">Penerima Terpilih</p>
                                <p class="text-xs text-emerald-700">Pemohon ini sudah ditetapkan sebagai penerima barang.</p>
                            @else
                                <p class="text-sm font-semibold text-rose-800">Permohonan Ditolak</p>
                                <p class="text-xs text-rose-700">Pemohon tidak terpilih karena barang diberikan ke pihak lain.</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="p-10 text-center text-sm text-gray-600 space-y-2">
                        <p class="font-medium text-gray-900">Belum ada permohonan yang masuk.</p>
                        <p>Barang Anda belum menerima permohonan atau semua permohonan tersaring oleh filter.</p>
                        <a href="{{ route('inventory.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-dashed border-teal-300 px-4 py-2 text-sm font-medium text-teal-700 hover:bg-teal-50">
                            Lihat daftar barang
                        </a>
                    </div>
                @endforelse
            </div>

            @if ($requests->hasPages())
                <div class="px-6 py-5 border-t border-gray-100">
                    {{ $requests->links() }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

