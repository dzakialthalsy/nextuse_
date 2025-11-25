@extends('layouts.app')

@section('title', 'Posting Barang - NextUse')

@section('content')
    <div class="py-8 px-4 sm:px-6">
        <div class="max-w-[1200px] mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('beranda') }}" class="text-gray-500 hover:text-gray-700">Jelajah</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </li>
                    <li class="text-gray-900 font-medium">Posting Barang</li>
                </ol>
            </nav>

            <div class="grid grid-cols-12 gap-6">
                <!-- Main Form -->
                <div class="col-span-12 lg:col-span-7">
                    <div class="max-w-[640px]">
                        @php
                            $editing = isset($item);
                            $oldPreferensi = old('preferensi', $editing ? ($item->preferensi ?? []) : []);
                            if (! is_array($oldPreferensi)) {
                                $oldPreferensi = [];
                            }
                            $existingPhotos = $editing && is_array($item->foto_barang) ? $item->foto_barang : [];
                        @endphp
                        <!-- Header -->
                        <div class="mb-8">
                            <h1 class="text-3xl font-semibold mb-2 text-gray-900">
                                {{ $editing ? 'Perbarui Barang' : 'Posting Barang' }}
                            </h1>
                            <p class="text-gray-600">
                                {{ $editing ? 'Perbarui detail barang yang sudah kamu bagikan.' : 'Bagikan atau barter barang Anda secara gratis.' }}
                            </p>
                        </div>

                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Form -->

                        <form action="{{ route('post-item.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="itemForm">
                            @csrf
                            @if($editing)
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                @foreach($existingPhotos as $foto)
                                    <input type="hidden" name="existing_foto_barang[]" value="{{ $foto }}">
                                @endforeach
                            @endif

                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="p-4 border border-red-300 bg-red-50 rounded-lg text-sm text-red-800">
                                    <p class="font-medium mb-2">Periksa kembali form Anda:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Judul -->
                            <div class="space-y-2">
                                <label for="judul" class="block text-sm font-medium text-gray-900">
                                    Judul Barang <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="judul"
                                    name="judul"
                                    value="{{ old('judul', $item->judul ?? '') }}"
                                    placeholder="Contoh: Kamera Digital Canon EOS 700D"
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('judul') border-red-500 @enderror"
                                    required
                                />
                                @error('judul')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="space-y-2">
                                <label for="kategori" class="block text-sm font-medium text-gray-900">
                                    Kategori <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="kategori"
                                    name="kategori"
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('kategori') border-red-500 @enderror"
                                    required
                                >
                                    <option value="">Pilih kategori barang</option>
                                    @foreach (['Elektronik','Perabotan','Pakaian','Buku & Alat Tulis','Mainan & Hobi','Olahraga','Dapur','Lainnya'] as $kategori)
                                        <option value="{{ $kategori }}" {{ old('kategori', $item->kategori ?? '') == $kategori ? 'selected' : '' }}>
                                            {{ $kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Kondisi -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-900">
                                    Kondisi Barang <span class="text-red-600">*</span>
                                </label>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" id="kondisi-baru" name="kondisi" value="baru" {{ old('kondisi', $item->kondisi ?? '') == 'baru' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500" required />
                                        <label for="kondisi-baru" class="text-sm text-gray-900 cursor-pointer">Baru</label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" id="kondisi-like-new" name="kondisi" value="like-new" {{ old('kondisi', $item->kondisi ?? '') == 'like-new' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500" required />
                                        <label for="kondisi-like-new" class="text-sm text-gray-900 cursor-pointer">Like New</label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" id="kondisi-bekas" name="kondisi" value="bekas" {{ old('kondisi', $item->kondisi ?? '') == 'bekas' ? 'checked' : '' }} class="w-4 h-4 text-teal-600 focus:ring-teal-500" required />
                                        <label for="kondisi-bekas" class="text-sm text-gray-900 cursor-pointer">Bekas</label>
                                    </div>
                                </div>
                                @error('kondisi')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Jumlah/Kuantitas -->
                            <div class="space-y-2">
                                <label for="jumlah" class="block text-sm font-medium text-gray-900">
                                    Jumlah Barang <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="jumlah"
                                    name="jumlah"
                                    min="1"
                                    value="{{ old('jumlah', $item->jumlah ?? 1) }}"
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('jumlah') border-red-500 @enderror"
                                    required
                                />
                                @error('jumlah')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <label for="deskripsi" class="block text-sm font-medium text-gray-900">
                                        Deskripsi <span class="text-red-600">*</span>
                                    </label>
                                    <button type="button" class="text-gray-500 hover:text-gray-700" title="Deskripsikan kondisi barang, alasan memberikan, dan informasi penting lainnya.">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    rows="5"
                                    placeholder="Ceritakan tentang barang ini, kondisinya, dan mengapa Anda ingin berbagi..."
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm resize-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('deskripsi') border-red-500 @enderror"
                                    required
                                    oninput="updateCharCount(this)"
                                >{{ old('deskripsi', $item->deskripsi ?? '') }}</textarea>
                                <p id="deskripsi-helper" class="text-gray-500 text-sm">
                                    Minimal 30 karakter. <span id="char-count">{{ strlen(old('deskripsi', $item->deskripsi ?? '')) }}</span>/30
                                </p>
                                @error('deskripsi')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Foto Barang -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-900">
                                    Foto Barang <span class="text-red-600">*</span>
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                    <input
                                        type="file"
                                        id="foto_barang"
                                        name="foto_barang[]"
                                        multiple
                                        accept="image/*"
                                        class="hidden"
                                        onchange="previewImages(this)"
                                        @unless($editing) required @endunless
                                    />
                                    <label for="foto_barang" class="cursor-pointer">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">Minimal 1 foto, maksimal 8 foto (maks 5MB per foto)</p>
                                    </label>
                                    <div id="image-preview" class="mt-4 grid grid-cols-4 gap-4 hidden"></div>
                                    @if($editing && count($existingPhotos))
                                        <div class="mt-4 text-left">
                                            <p class="text-xs text-gray-500 mb-2">Foto saat ini:</p>
                                            <div class="grid grid-cols-3 gap-3">
                                                @foreach($existingPhotos as $foto)
                                                    @php
                                                        $fotoUrl = \Illuminate\Support\Str::startsWith($foto, ['http://', 'https://'])
                                                            ? $foto
                                                            : asset('storage/'.$foto);
                                                    @endphp
                                                    <img src="{{ $fotoUrl }}" alt="Foto {{ $loop->iteration }}" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                                @endforeach
                                            </div>
                                            <p class="text-xs text-gray-400 mt-2">Unggah foto baru untuk menambahkan gambar tambahan.</p>
                                        </div>
                                    @endif
                                </div>
                                @error('foto_barang')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Lokasi -->
                            <div class="space-y-2">
                                <label for="lokasi" class="block text-sm font-medium text-gray-900">
                                    Lokasi <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="lokasi"
                                    name="lokasi"
                                    value="{{ old('lokasi', $item->lokasi ?? '') }}"
                                    placeholder="Cari lokasi... (e.g., Jakarta Selatan)"
                                    list="lokasi-suggestions"
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('lokasi') border-red-500 @enderror"
                                    required
                                />
                                <datalist id="lokasi-suggestions">
                                    <option value="Jakarta Pusat">
                                    <option value="Jakarta Selatan">
                                    <option value="Jakarta Utara">
                                    <option value="Jakarta Timur">
                                    <option value="Jakarta Barat">
                                    <option value="Bandung">
                                    <option value="Surabaya">
                                    <option value="Yogyakarta">
                                </datalist>
                                <p class="text-gray-500 text-sm">Masukkan kota atau area Anda</p>
                                @error('lokasi')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-medium text-gray-900">Status Barang</label>
                                <select
                                    id="status"
                                    name="status"
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                >
                                    @foreach (['tersedia' => 'Tersedia', 'reserved' => 'Reserved', 'habis' => 'Habis'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $item->status ?? 'tersedia') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-gray-500 text-sm">Status saat ini untuk barang ini</p>
                            </div>

                            <!-- Preferensi -->
                            <div class="space-y-3">
                                <label class="block text-sm font-medium text-gray-900">Preferensi</label>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            id="pref-giveaway"
                                            name="preferensi[]"
                                            value="giveaway"
                                            {{ in_array('giveaway', $oldPreferensi) ? 'checked' : '' }}
                                            class="w-4 h-4 text-teal-600 focus:ring-teal-500 rounded"
                                        />
                                        <label for="pref-giveaway" class="text-sm text-gray-900 cursor-pointer">Giveaway (Gratis)</label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            id="pref-barter"
                                            name="preferensi[]"
                                            value="barter"
                                            {{ in_array('barter', $oldPreferensi) ? 'checked' : '' }}
                                            class="w-4 h-4 text-teal-600 focus:ring-teal-500 rounded"
                                        />
                                        <label for="pref-barter" class="text-sm text-gray-900 cursor-pointer">Barter (Tukar barang)</label>
                                    </div>
                                </div>
                                <p class="text-gray-500 text-sm">Pilih satu atau lebih opsi</p>
                            </div>

                            <!-- Catatan Pengambilan -->
                            <div class="space-y-2">
                                <label for="catatan_pengambilan" class="block text-sm font-medium text-gray-900">Catatan Pengambilan (Opsional)</label>
                                <textarea
                                    id="catatan_pengambilan"
                                    name="catatan_pengambilan"
                                    rows="3"
                                    placeholder="Contoh: COD area Jakarta Selatan, atau bisa kirim dengan biaya ongkir..."
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm resize-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                >{{ old('catatan_pengambilan', $item->catatan_pengambilan ?? '') }}</textarea>
                                <p class="text-gray-500 text-sm">Informasi tambahan tentang cara pengambilan</p>
                            </div>

                            <!-- Syarat Ketentuan Pemohon -->
                            <div class="space-y-2">
                                <label for="applicant_requirements" class="block text-sm font-medium text-gray-900">Syarat Ketentuan Pemohon (Opsional)</label>
                                <textarea
                                    id="applicant_requirements"
                                    name="applicant_requirements"
                                    rows="4"
                                    placeholder="Contoh: Prioritas untuk panti asuhan, wajib menunjukkan surat kebutuhan, dll."
                                    class="w-full px-4 py-2 border rounded-lg shadow-sm resize-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                >{{ old('applicant_requirements', $item->applicant_requirements ?? '') }}</textarea>
                                <p class="text-gray-500 text-sm">Gunakan bagian ini untuk menjelaskan kriteria penerima yang memenuhi hibah.</p>
                            </div>

                            <!-- Kebijakan -->
                            <div class="flex items-start space-x-2 p-4 rounded-lg border border-gray-200 bg-gray-50">
                                <input
                                    type="checkbox"
                                    id="setuju_kebijakan"
                                    name="setuju_kebijakan"
                                    value="1"
                                    class="mt-1 w-4 h-4 text-teal-600 focus:ring-teal-500 rounded @error('setuju_kebijakan') border-red-500 @enderror"
                                    {{ old('setuju_kebijakan', $editing ? true : false) ? 'checked' : '' }}
                                    required
                                />
                                <div class="flex-1">
                                    <label for="setuju_kebijakan" class="text-sm text-gray-900 cursor-pointer">
                                        Saya setuju dengan
                                        <a href="{{ route('syarat-ketentuan') }}" class="text-teal-600 hover:text-teal-700 underline" target="_blank">
                                            Syarat dan Ketentuan
                                        </a>
                                        NextUse <span class="text-red-600">*</span>
                                    </label>
                                    @error('setuju_kebijakan')
                                        <p class="text-red-600 text-sm flex items-center gap-1 mt-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mobile Submit Buttons -->
                            <div class="lg:hidden flex flex-col gap-3 pt-4">
                                <button
                                    type="submit"
                                    class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-2 px-4 rounded-lg w-full flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Terbitkan
                                </button>
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="text-gray-700 font-medium py-2 px-4 rounded-lg w-full hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                            </div>

                            <!-- Desktop Submit Buttons -->
                            <div class="hidden lg:flex items-center justify-between pt-4 border-t border-gray-200">
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Terbitkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview & Profil Pengunggah - Desktop Only -->
                <div class="hidden lg:block col-span-5">
                    <div class="sticky top-24">
                        @isset($profile)
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm mb-4">
                                <h3 class="text-lg font-semibold mb-3 text-gray-900">Profil Pengunggah</h3>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(mb_substr($profile->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $profile->full_name }}</p>
                                        @if ($profile->location)
                                            <p class="text-xs text-gray-500">📍 {{ $profile->location }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endisset
                        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900">Preview</h3>
                            <div class="space-y-4">
                                <div id="preview-content" class="text-gray-500 text-sm">
                                    <p>Isi form untuk melihat preview</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateCharCount(textarea) {
            const count = textarea.value.length;
            document.getElementById('char-count').textContent = count;
        }

        function previewImages(input) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            preview.classList.remove('hidden');

            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file, index) => {
                    if (index >= 8) return;
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-24 object-cover rounded-lg">
                            <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        function removeImage(index) {
            const input = document.getElementById('foto_barang');
            const dt = new DataTransfer();
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) dt.items.add(file);
            });
            input.files = dt.files;
            previewImages(input);
        }

        // Update preview on form change
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('itemForm');
            const previewContent = document.getElementById('preview-content');
            const deskripsiField = document.getElementById('deskripsi');

            if (deskripsiField) {
                updateCharCount(deskripsiField);
            }
            
            if (form && previewContent) {
                const updatePreview = () => {
                    const judul = document.getElementById('judul').value || 'Judul Barang';
                    const kategori = document.getElementById('kategori').value || 'Kategori';
                    const kondisi = document.querySelector('input[name="kondisi"]:checked')?.value || 'Kondisi';
                    const deskripsi = document.getElementById('deskripsi').value || 'Deskripsi akan muncul di sini...';
                    const lokasi = document.getElementById('lokasi').value || 'Lokasi';
                    
                    previewContent.innerHTML = `
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-900">${judul}</h4>
                            <div class="flex gap-2">
                                <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs rounded-full">${kategori}</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">${kondisi}</span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-3">${deskripsi}</p>
                            <p class="text-xs text-gray-500">📍 ${lokasi}</p>
                        </div>
                    `;
                };

                form.addEventListener('input', updatePreview);
                updatePreview();
            }
        });
    </script>
@endpush
