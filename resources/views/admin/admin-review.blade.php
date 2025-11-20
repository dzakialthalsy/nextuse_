<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meninjau Laporan - NextUse Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @layer utilities {
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-teal-50 via-green-50 to-emerald-50 flex flex-col">
    <!-- Header -->
    <header class="bg-white border-b border-[rgba(0,0,0,0.1)] sticky top-0 z-50">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-[#00bba7] to-[#009966] rounded-lg flex items-center justify-center">
                        <span class="text-white">N</span>
                    </div>
                    <span class="text-neutral-950">NextUse Admin</span>
                </div>
                <nav class="hidden md:flex items-center gap-6 text-[#717182]">
                    @php
                        $isKelola = request()->routeIs('admin.mengelola-data.index');
                        $isTinjau = request()->routeIs('admin.tinjau') || request()->routeIs('admin.review.*');
                    @endphp
                    <a href="{{ route('admin.mengelola-data.index') }}" class="{{ $isKelola ? 'text-neutral-950 border-b-2 border-teal-500 font-medium' : 'hover:text-neutral-950' }}">Kelola</a>
                    <a href="{{ route('admin.tinjau') }}" class="{{ $isTinjau ? 'text-neutral-950 border-b-2 border-teal-500 font-medium' : 'hover:text-neutral-950' }}">Tinjau</a>
                </nav>
                <div class="flex items-center gap-3">
                    @if(session('organization_id'))
                        <span class="text-sm text-[#717182] hidden md:block">{{ session('organization_name') }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-sm text-[#717182] hover:text-neutral-950 rounded-lg">
                                Logout
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-8 px-4 sm:px-6 pb-32">
        <div class="max-w-[1080px] mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="#" onclick="window.history.back(); return false;" class="inline-flex items-center gap-2 text-[#717182] hover:text-neutral-950 mb-4 -ml-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Antrian
                </a>

                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-semibold text-neutral-950">Meninjau Laporan {{ $reportType === 'item' ? 'Postingan' : 'Pengguna' }}</h1>
                                <p class="text-sm text-[#717182]">
                                    Moderator dapat melihat detail laporan, memeriksa bukti, dan mengambil tindakan pada konten yang dilaporkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    @php
                        $statusBadge = match($report['status']) {
                            'resolved' => ['bg-green-100', 'text-green-700', 'border-green-300', 'Resolved'],
                            'rejected' => ['bg-red-100', 'text-red-700', 'border-red-300', 'Rejected'],
                            'duplicate' => ['bg-gray-100', 'text-gray-700', 'border-gray-300', 'Duplicate'],
                            default => ['bg-yellow-100', 'text-yellow-700', 'border-yellow-300', 'Pending'],
                        };
                    @endphp
                    <span class="px-3 py-1 text-sm font-medium rounded-full border {{ $statusBadge[0] }} {{ $statusBadge[1] }} {{ $statusBadge[2] }}">
                        {{ $statusBadge[3] }}
                    </span>
                </div>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 border border-[#009689]/30 bg-teal-50 text-sm text-neutral-950 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 border border-[#d4183d]/30 bg-red-50 text-sm text-[#d4183d] rounded-lg">
                    <p class="font-medium mb-2">Terjadi kesalahan:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6">
                <!-- Informasi Laporan -->
                <div class="bg-white rounded-lg border border-neutral-200 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-950">Informasi Laporan</h3>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-[#717182] mb-1">ID Laporan</label>
                                <p class="font-mono text-neutral-950">{{ $report['reportId'] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Tanggal Laporan</label>
                                <p class="text-neutral-950">{{ \Carbon\Carbon::parse($report['date'])->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Pelapor</label>
                                <a href="#" class="flex items-center gap-2 hover:text-[#009689] transition-colors text-neutral-950">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ $report['reporter']['nama'] }}</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Kategori Pelanggaran</label>
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-sm border border-neutral-200 rounded">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ $report['category'] }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Deskripsi Laporan</label>
                                <p class="text-sm mt-1 p-3 bg-[#f3f3f5] rounded-lg border border-neutral-200 text-neutral-950">
                                    {{ $report['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($reportType === 'item' && isset($report['item']))
                <!-- Detail Postingan -->
                <div class="bg-white rounded-lg border border-neutral-200 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-950">Detail Postingan</h3>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <!-- Photos -->
                        <div>
                            <label class="block text-sm text-[#717182] mb-2">Foto Barang</label>
                            <div class="grid grid-cols-2 gap-2">
                                @if(!empty($report['item']['photos']))
                                    @foreach($report['item']['photos'] as $photo)
                                        <div class="aspect-square rounded-lg overflow-hidden bg-[#f3f3f5]">
                                            <img src="{{ $photo }}" alt="Photo" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="aspect-square rounded-lg bg-[#f3f3f5] flex items-center justify-center">
                                        <span class="text-xs text-[#717182]">Tidak ada foto</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Judul Postingan</label>
                                <h4 class="mt-1 text-neutral-950 font-medium">{{ $report['item']['title'] }}</h4>
                            </div>

                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Pemilik</label>
                                <a href="#" class="flex items-center gap-2 mt-1 hover:text-[#009689] transition-colors text-neutral-950">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ $report['item']['owner']['nama'] }}</span>
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>

                            <div>
                                <label class="block text-sm text-[#717182] mb-1">Deskripsi Barang</label>
                                <p class="text-sm mt-1 text-neutral-950">{{ $report['item']['description'] }}</p>
                            </div>

                            <div class="flex items-center gap-4 flex-wrap">
                                <div>
                                    <label class="block text-sm text-[#717182] mb-1">Status Postingan</label>
                                    <span class="inline-block px-2 py-1 text-sm border border-neutral-200 rounded">
                                        {{ ucfirst($report['item']['status']) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm text-[#717182] mb-1">Tanggal Posting</label>
                                    <p class="text-sm mt-1 text-neutral-950">
                                        {{ \Carbon\Carbon::parse($report['item']['postedAt'])->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                    </p>
                                </div>
                            </div>

                            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 border border-neutral-200 rounded-lg hover:bg-gray-50 transition-colors text-neutral-950">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Lihat Halaman Barang
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($report['evidence']))
                <!-- Bukti -->
                <div class="bg-white rounded-lg border border-neutral-200 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-950">Bukti Lampiran</h3>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" id="evidence-gallery">
                        @foreach($report['evidence'] as $evidence)
                            <div class="group relative overflow-hidden rounded-lg border border-neutral-200 cursor-pointer hover:shadow-md transition-shadow bg-white" onclick="openEvidenceModal('{{ $evidence['url'] }}', '{{ $evidence['filename'] }}', '{{ $evidence['type'] }}')">
                                <div class="aspect-square bg-[#f3f3f5] flex items-center justify-center">
                                    @if($evidence['type'] === 'image')
                                        <img src="{{ $evidence['url'] }}" alt="{{ $evidence['filename'] }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-12 h-12 text-[#717182]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @endif
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="p-2 border-t border-neutral-200">
                                    <p class="text-xs truncate text-neutral-950" title="{{ $evidence['filename'] }}">{{ $evidence['filename'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($report['moderationHistory']))
                <!-- Riwayat Moderasi -->
                <div class="bg-white rounded-lg border border-neutral-200 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-950">Riwayat Moderasi</h3>
                    </div>
                    <div class="space-y-3">
                        @foreach($report['moderationHistory'] as $history)
                            <div class="flex gap-3 p-4 bg-[#f3f3f5] rounded-lg border border-neutral-200">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-medium text-neutral-950">{{ $history['moderator']['nama'] }}</span>
                                            @php
                                                $historyStatusBadge = match($history['status']) {
                                                    'approved' => ['bg-green-50', 'border-green-200', 'text-green-700', 'Disetujui'],
                                                    'rejected' => ['bg-red-50', 'border-red-200', 'text-red-700', 'Ditolak'],
                                                    'escalated' => ['bg-orange-50', 'border-orange-200', 'text-orange-700', 'Dieskalasi'],
                                                    default => ['bg-blue-50', 'border-blue-200', 'text-blue-700', 'Menunggu'],
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs border rounded {{ $historyStatusBadge[0] }} {{ $historyStatusBadge[1] }} {{ $historyStatusBadge[2] }}">
                                                {{ $historyStatusBadge[3] }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-[#717182] shrink-0">
                                            {{ \Carbon\Carbon::parse($history['timestamp'])->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-[#717182] mb-1">{{ $history['action'] }}</p>
                                    @if(!empty($history['reason']))
                                        <p class="text-sm italic text-[#717182] bg-white/50 p-2 rounded mt-2">
                                            &ldquo;{{ $history['reason'] }}&rdquo;
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Actions Form -->
                <div class="bg-white rounded-lg border border-neutral-200 p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-5 h-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-neutral-950">Tindakan Moderasi</h3>
                    </div>

                    <form action="{{ route('admin.review.update', ['type' => $reportType, 'id' => $report['id']]) }}" method="POST" id="moderationForm">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Step 1: Decision -->
                            <div>
                                <label class="block mb-3 text-neutral-950 font-medium">1. Pilih Keputusan</label>
                                <div class="space-y-3">
                                    <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors {{ old('decision') === 'reject' || $reportModel->decision === 'reject' ? 'border-[#009689] bg-teal-50' : 'border-neutral-200 hover:border-teal-300' }}">
                                        <input type="radio" name="decision" value="reject" class="mt-1" {{ old('decision') === 'reject' || $reportModel->decision === 'reject' ? 'checked' : '' }} onchange="handleDecisionChange()">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <svg class="w-4 h-4 text-[#d4183d]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span class="font-medium text-neutral-950">Tolak Laporan</span>
                                            </div>
                                            <p class="text-sm text-[#717182]">
                                                Laporan tidak valid atau tidak memiliki bukti yang cukup
                                            </p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors {{ old('decision') === 'accept' || $reportModel->decision === 'accept' ? 'border-[#009689] bg-teal-50' : 'border-neutral-200 hover:border-teal-300' }}">
                                        <input type="radio" name="decision" value="accept" class="mt-1" {{ old('decision') === 'accept' || $reportModel->decision === 'accept' ? 'checked' : '' }} onchange="handleDecisionChange()">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium text-neutral-950">Terima Laporan</span>
                                            </div>
                                            <p class="text-sm text-[#717182]">
                                                Laporan valid dan memerlukan tindakan moderasi
                                            </p>
                                        </div>
                                    </label>
                                </div>
                                @error('decision')
                                    <p class="text-sm text-[#d4183d] mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Reject Reason -->
                            <div id="reject-reason-section" class="pl-4 border-l-2 border-[#009689] space-y-3" style="display: {{ old('decision') === 'reject' || $reportModel->decision === 'reject' ? 'block' : 'none' }};">
                                <label for="reject-reason" class="block text-neutral-950 font-medium">Alasan Penolakan *</label>
                                <textarea
                                    id="reject-reason"
                                    name="reject_reason"
                                    rows="4"
                                    placeholder="Jelaskan mengapa laporan ini ditolak..."
                                    class="w-full px-3 py-2 bg-[#f3f3f5] rounded-lg text-sm border border-neutral-200 shadow-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent {{ $errors->has('reject_reason') ? 'border-[#d4183d]' : '' }}"
                                >{{ old('reject_reason', $reportModel->reject_reason) }}</textarea>
                                @error('reject_reason')
                                    <p class="text-sm text-[#d4183d] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Step 2: Action (if accept) -->
                            <div id="action-section" class="pl-4 border-l-2 border-[#009689] space-y-4" style="display: {{ old('decision') === 'accept' || $reportModel->decision === 'accept' ? 'block' : 'none' }};">
                                <label class="block text-neutral-950 font-medium">2. Pilih Tindakan</label>
                                <div class="space-y-3">
                                    @php
                                        $actions = [
                                            'delete' => ['Hapus Postingan', 'Postingan akan dihapus secara permanen dan tidak dapat dipulihkan.', 'text-[#d4183d]'],
                                            'suspend' => ['Sembunyikan/Suspend Postingan', 'Postingan akan disembunyikan dari publik. Pemilik dapat mengajukan banding.', 'text-orange-600'],
                                            'warn' => ['Berikan Peringatan ke Pemilik', 'Pemilik akan menerima peringatan resmi melalui email dan notifikasi.', 'text-yellow-600'],
                                            'no-action' => ['Tidak Ada Tindakan Lanjut', 'Laporan dicatat tanpa tindakan terhadap postingan.', 'text-[#717182]'],
                                        ];
                                    @endphp
                                    @foreach($actions as $actionValue => $actionInfo)
                                        <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors {{ old('action') === $actionValue || $reportModel->action === $actionValue ? 'border-[#009689] bg-teal-50' : 'border-neutral-200 hover:border-teal-300' }}">
                                            <input type="radio" name="action" value="{{ $actionValue }}" class="mt-1" {{ old('action') === $actionValue || $reportModel->action === $actionValue ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    @if($actionValue === 'delete')
                                                        <svg class="w-4 h-4 {{ $actionInfo[2] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    @elseif($actionValue === 'suspend')
                                                        <svg class="w-4 h-4 {{ $actionInfo[2] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                        </svg>
                                                    @elseif($actionValue === 'warn')
                                                        <svg class="w-4 h-4 {{ $actionInfo[2] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 {{ $actionInfo[2] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    @endif
                                                    <span class="font-medium text-neutral-950">{{ $actionInfo[0] }}</span>
                                                </div>
                                                <p class="text-sm text-[#717182]">
                                                    {{ $actionInfo[1] }}
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('action')
                                    <p class="text-sm text-[#d4183d] mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <!-- Action Note -->
                                <div id="action-note-section" class="mt-4" style="display: {{ old('action') || $reportModel->action ? 'block' : 'none' }};">
                                    <label for="action-note" class="block text-neutral-950 font-medium">Catatan Tambahan (Opsional)</label>
                                    <textarea
                                        id="action-note"
                                        name="action_note"
                                        rows="3"
                                        placeholder="Tambahkan catatan untuk tindakan ini..."
                                        class="w-full px-3 py-2 bg-[#f3f3f5] rounded-lg text-sm border border-neutral-200 shadow-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent mt-2"
                                    >{{ old('action_note', $reportModel->action_note) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Sticky Action Footer -->
    <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-sm border-t border-[rgba(0,0,0,0.1)] py-4 px-4 sm:px-6 z-10">
        <div class="max-w-[1080px] mx-auto flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 border border-neutral-200 text-neutral-950 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tandai Menunggu Bukti Tambahan
                </button>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="window.history.back();" class="px-4 py-2 border border-neutral-200 text-neutral-950 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </button>
                <button type="submit" form="moderationForm" id="submitBtn" class="px-4 py-2 bg-gradient-to-r from-[#00bba7] to-[#009966] text-white rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span id="submitText">Simpan Keputusan</span>
                    <svg id="submitLoader" class="h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Evidence Modal -->
    <div id="evidenceModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="closeEvidenceModal()">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="absolute top-0 left-0 right-0 z-10 bg-white/95 backdrop-blur-sm border-b border-neutral-200 p-4 flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p id="modal-filename" class="truncate text-neutral-950 font-medium"></p>
                    <p id="modal-type" class="text-xs text-[#717182]"></p>
                </div>
                <button onclick="closeEvidenceModal()" class="ml-4 p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="pt-20 pb-4 px-4 max-h-[80vh] overflow-auto">
                <img id="modal-image" src="" alt="" class="w-full h-auto hidden">
                <div id="modal-pdf" class="aspect-[8.5/11] bg-[#f3f3f5] flex items-center justify-center hidden">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-[#717182] mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm text-[#717182]">Preview PDF tidak tersedia</p>
                        <a id="modal-download" href="#" download class="inline-flex items-center gap-2 mt-4 px-4 py-2 border border-neutral-200 rounded-lg hover:bg-gray-50 transition-colors text-neutral-950">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh untuk Melihat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleDecisionChange() {
            const decision = document.querySelector('input[name="decision"]:checked')?.value;
            const rejectSection = document.getElementById('reject-reason-section');
            const actionSection = document.getElementById('action-section');
            
            if (decision === 'reject') {
                rejectSection.style.display = 'block';
                actionSection.style.display = 'none';
            } else if (decision === 'accept') {
                rejectSection.style.display = 'none';
                actionSection.style.display = 'block';
            }
        }

        // Handle action change to show action note
        document.querySelectorAll('input[name="action"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const actionNoteSection = document.getElementById('action-note-section');
                if (this.checked) {
                    actionNoteSection.style.display = 'block';
                }
            });
        });

        function openEvidenceModal(url, filename, type) {
            const modal = document.getElementById('evidenceModal');
            const modalImage = document.getElementById('modal-image');
            const modalPdf = document.getElementById('modal-pdf');
            const modalFilename = document.getElementById('modal-filename');
            const modalType = document.getElementById('modal-type');
            const modalDownload = document.getElementById('modal-download');

            modalFilename.textContent = filename;
            modalType.textContent = type === 'image' ? 'Gambar' : 'Dokumen PDF';
            modalDownload.href = url;

            if (type === 'image') {
                modalImage.src = url;
                modalImage.classList.remove('hidden');
                modalPdf.classList.add('hidden');
            } else {
                modalImage.classList.add('hidden');
                modalPdf.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeEvidenceModal() {
            document.getElementById('evidenceModal').classList.add('hidden');
        }

        // Form submit handler
        const form = document.getElementById('moderationForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');

        form.addEventListener('submit', function(e) {
            const decision = document.querySelector('input[name="decision"]:checked')?.value;
            const action = document.querySelector('input[name="action"]:checked')?.value;

            if (!decision) {
                e.preventDefault();
                alert('Silakan pilih keputusan terlebih dahulu.');
                return;
            }

            if (decision === 'reject') {
                const rejectReason = document.getElementById('reject-reason').value.trim();
                if (!rejectReason) {
                    e.preventDefault();
                    alert('Silakan masukkan alasan penolakan.');
                    return;
                }
            }

            if (decision === 'accept' && !action) {
                e.preventDefault();
                alert('Silakan pilih tindakan terlebih dahulu.');
                return;
            }

            // Check for destructive actions
            if (decision === 'accept' && (action === 'delete' || action === 'suspend')) {
                const confirmMessage = action === 'delete' 
                    ? 'Anda akan menghapus postingan secara permanen. Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin melanjutkan?'
                    : 'Anda akan menangguhkan postingan ini. Postingan akan disembunyikan dari publik. Apakah Anda yakin ingin melanjutkan?';
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return;
                }
            }

            submitBtn.disabled = true;
            submitText.textContent = 'Menyimpan...';
            submitLoader.classList.remove('hidden');
        });
    </script>
</body>
</html>

