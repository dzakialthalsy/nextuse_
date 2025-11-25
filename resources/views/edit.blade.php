@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
@php
    $oldPreferensi = old('preferensi', $item->preferensi ?? []);
    if (!is_array($oldPreferensi)) { $oldPreferensi = []; }
@endphp

<div class="hero-bg py-12 px-6 sm:px-10 border-b border-gray-100">
    <div class="max-w-4xl mx-auto text-center space-y-4">
        <p class="text-sm font-semibold tracking-wide uppercase text-teal-600">Inventori Saya</p>
        <h1 class="text-3xl font-semibold text-gray-900">Edit Barang</h1>
        <p class="text-gray-600 text-base">
            Perbarui informasi barang agar tetap akurat untuk komunitas NextUse.
        </p>
    </div>
    @if($errors->any())
        <div class="max-w-3xl mx-auto mt-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
            <ul class="list-disc px-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Judul Barang</label>
                        <input type="text" name="judul" value="{{ old('judul', $item->judul) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Kategori</label>
                        <select name="kategori" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                            @foreach (['Elektronik','Perabotan','Pakaian','Buku & Alat Tulis','Mainan & Hobi','Olahraga','Dapur','Lainnya'] as $kategori)
                                <option value="{{ $kategori }}" {{ old('kategori', $item->kategori) == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Kondisi</label>
                        <select name="kondisi" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                            @foreach (['baru' => 'Baru', 'like-new' => 'Seperti Baru', 'bekas' => 'Bekas'] as $value => $label)
                                <option value="{{ $value }}" {{ old('kondisi', $item->kondisi) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Lokasi</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $item->lokasi) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Deskripsi</label>
                        <textarea name="deskripsi" rows="6" oninput="updateCharCount(this)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Karakter: <span id="char-count">{{ strlen(old('deskripsi', $item->deskripsi ?? '')) }}</span></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Jumlah</label>
                        <input type="number" name="jumlah" min="1" value="{{ old('jumlah', $item->jumlah ?? 1) }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Status</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                            @foreach (['tersedia' => 'Tersedia', 'reserved' => 'Reserved', 'habis' => 'Habis'] as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $item->status ?? 'tersedia') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Preferensi</label>
                        <div class="space-y-2">
                            @foreach(['giveaway' => 'Giveaway', 'barter' => 'Barter'] as $pref => $label)
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="preferensi[]" value="{{ $pref }}" {{ in_array($pref, $oldPreferensi) ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500 rounded">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Catatan Pengambilan (Opsional)</label>
                        <textarea name="catatan_pengambilan" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900" placeholder="Contoh: COD area Jakarta Selatan, atau bisa kirim dengan biaya ongkir...">{{ old('catatan_pengambilan', $item->catatan_pengambilan) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Syarat Ketentuan Pemohon (Opsional)</label>
                        <textarea name="applicant_requirements" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900" placeholder="Tuliskan kriteria penerima yang diutamakan, dokumen yang diperlukan, dsb.">{{ old('applicant_requirements', $item->applicant_requirements) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-900">Foto Barang</label>
                <input type="file" name="foto_barang[]" multiple accept="image/*" onchange="previewImages(this)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-gray-900">
                <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-3 {{ empty($item->foto_barang) ? 'hidden' : '' }}">
                    @if(!empty($item->foto_barang))
                        @foreach($item->foto_barang as $foto)
                            <div class="relative">
                                <img src="{{ asset('storage/'.$foto) }}" alt="Foto" class="w-full h-32 object-cover rounded-xl border border-gray-200">
                                <input type="hidden" name="foto_barang_old[]" value="{{ $foto }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('inventory.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl main-gradient text-white font-semibold">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</main>

<script>
    function updateCharCount(textarea) {
        const count = textarea.value.length;
        const el = document.getElementById('char-count');
        if (el) { el.textContent = count; }
    }
    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        if (!preview) return;
        preview.innerHTML = '';
        preview.classList.remove('hidden');
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-32 object-cover rounded-xl border border-gray-200" alt="Preview">`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endsection
