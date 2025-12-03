@extends('layouts.app')

@section('title', 'Ajukan Permohonan Barang - ' . $item->judul)

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <a href="{{ route('items.show', $item->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke detail barang
        </a>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="space-y-1">
                <p class="text-sm text-gray-500">Mengajukan permohonan untuk</p>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $item->judul }}</h1>
                <div class="text-sm text-gray-500">
                    Dibagikan oleh {{ $item->organization->organization_name ?? 'Organisasi NextUse' }}
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="p-4 rounded-xl bg-gray-50">
                    <p class="text-xs uppercase text-gray-500 tracking-wide">Lokasi</p>
                    <p class="text-base font-semibold text-gray-900">{{ $item->lokasi }}</p>
                </div>
                <div class="p-4 rounded-xl bg-gray-50">
                    <p class="text-xs uppercase text-gray-500 tracking-wide">Jumlah tersedia</p>
                    <p class="text-base font-semibold text-gray-900">{{ $item->jumlah }} unit</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-xl border border-teal-100 bg-teal-50/60 p-4">
                <div>
                    <p class="text-sm font-semibold text-teal-900">Gunakan template Surat Permohonan Hibah</p>
                    <p class="text-sm text-teal-800">Unduh, isi, lalu unggah kembali bersama permohonan.</p>
                </div>
                <a href="{{ route('item-requests.template') }}"
                   class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition">
                    Unduh SuratPermohonanHibah.docx
                </a>
            </div>

            @if($latestRequest)
                <div class="p-4 rounded-xl border border-amber-200 bg-amber-50 text-sm text-amber-900">
                    <p class="font-semibold mb-1">Riwayat pengajuan Anda</p>
                    <p class="mb-1">Pengajuan terakhir: <span class="font-medium">{{ $latestRequest->created_at->format('d M Y H:i') }}</span></p>
                    <p>Status: <span class="capitalize font-medium">{{ $latestRequest->status }}</span></p>
                </div>
            @endif

            <form action="{{ route('item-requests.store', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="requested_quantity" class="block text-sm font-medium text-gray-700">
                        Jumlah yang diajukan
                    </label>
                    <input
                        type="number"
                        name="requested_quantity"
                        id="requested_quantity"
                        min="1"
                        max="{{ $item->jumlah }}"
                        value="{{ old('requested_quantity', 1) }}"
                        class="w-full rounded-xl border-gray-200 focus:border-teal-500 focus:ring-teal-500"
                        required
                    >
                    <p class="text-xs text-gray-500">Maksimum {{ $item->jumlah }} unit atau sesuai ketersediaan.</p>
                    @error('requested_quantity')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label for="surat_kuasa" class="block text-sm font-medium text-gray-700">
                        Unggah Surat Permohonan Hibah (doc, docx, pdf)
                    </label>
                    <div id="document-upload-area" class="border-2 border-dashed border-[#009689] bg-teal-50 rounded-[10px] p-6 transition-colors hover:border-teal-600">
                        <input id="surat_kuasa" type="file" name="surat_kuasa" accept=".pdf,.doc,.docx" class="hidden" required>
                        <label for="surat_kuasa" class="flex flex-col items-center gap-3 cursor-pointer">
                            <div class="p-3 bg-white rounded-full shadow-sm border border-[#009689]">
                                <svg class="h-6 w-6 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p id="surat_kuasa_filename" class="text-sm text-neutral-950">
                                    Klik untuk pilih file
                                </p>
                                <p class="text-xs text-[#717182] mt-1">
                                    PDF atau DOC (Maks. 10MB)
                                </p>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">Pastikan dokumen telah ditandatangani pimpinan atau penanggung jawab.</p>
                    @error('surat_kuasa')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 rounded-xl bg-teal-600 text-white font-semibold hover:bg-teal-700 transition">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('surat_kuasa');
        const fileNameEl = document.getElementById('surat_kuasa_filename');

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    fileNameEl.textContent = this.files[0].name;
                } else {
                    fileNameEl.textContent = 'Klik untuk pilih file';
                }
            });
        }
    });
</script>
@endpush
@endsection
