@extends('layouts.app')

@section('title', 'Laporkan Postingan - NextUse')

@section('content')
    <div class="min-h-[calc(100vh-180px)] bg-gradient-to-br from-teal-50 via-green-50 to-emerald-50 py-8 px-4 sm:px-6">
        <div class="max-w-[1200px] mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6 flex items-center gap-2 text-sm text-[#717182]">
                <a href="#" class="hover:text-neutral-950">Detail Postingan</a>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-neutral-950">Laporkan</span>
            </nav>

            <div class="grid grid-cols-12 gap-6">
                <!-- Main Form -->
                <div class="col-span-12 lg:col-start-3 lg:col-span-8">
                    <div class="max-w-[640px] mx-auto">
                        <!-- Header -->
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-3">
                                <h1 class="text-2xl font-semibold text-neutral-950">Laporkan Postingan/Barang</h1>
                                <button type="button" class="text-[#717182] hover:text-neutral-950" title="Laporan akan ditinjau oleh tim kami dalam 24-48 jam. Informasi Anda akan dijaga kerahasiaannya.">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[#717182]">
                                Bantu kami menjaga komunitas tetap aman.
                            </p>
                        </div>

                        <!-- Success/Error Messages -->
                        @if(session('status'))
                            <div class="mb-6 p-4 border border-[#009689]/30 bg-teal-50 text-sm text-neutral-950 rounded-lg">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(session('duplicate_warning'))
                            <div class="mb-6 p-4 border border-yellow-500 bg-yellow-50 rounded-lg flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-yellow-800 text-sm">
                                        Anda sudah melaporkan postingan ini. 
                                        <a href="#" class="underline hover:no-underline">Lihat status report</a>
                                    </p>
                                </div>
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

                        <!-- Form -->
                        <form action="{{ route('report-item.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="reportForm">
                            @csrf
                            
                            <!-- Judul Postingan - Readonly -->
                            <div class="space-y-2">
                                <label class="block text-sm text-neutral-950">Judul Postingan</label>
                                <div class="flex items-center gap-3 p-4 rounded-lg border border-neutral-200 bg-[#f3f3f5]">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#00bba7] to-[#009966] rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="truncate text-neutral-950 font-medium">{{ $itemTitle ?? 'Kamera Digital Canon EOS 700D - Kondisi Mulus' }}</p>
                                        <p class="text-sm text-[#717182]">{{ $itemId ?? 'ID: #12345' }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="item_id" value="{{ $itemId ?? '#12345' }}">
                                <input type="hidden" name="item_title" value="{{ $itemTitle ?? 'Kamera Digital Canon EOS 700D - Kondisi Mulus' }}">
                            </div>

                            <!-- Kategori Pelanggaran -->
                            <div class="space-y-2">
                                <label for="kategori" class="block text-sm text-neutral-950">
                                    Kategori Pelanggaran <span class="text-[#d4183d]">*</span>
                                </label>
                                <select
                                    id="kategori"
                                    name="kategori"
                                    class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-lg text-sm border border-neutral-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent {{ $errors->has('kategori') ? 'border-[#d4183d]' : '' }}"
                                >
                                    <option value="">Pilih kategori pelanggaran</option>
                                    <option value="konten-tidak-pantas" {{ old('kategori') == 'konten-tidak-pantas' ? 'selected' : '' }}>Konten Tidak Pantas</option>
                                    <option value="barang-palsu" {{ old('kategori') == 'barang-palsu' ? 'selected' : '' }}>Barang Palsu</option>
                                    <option value="spam" {{ old('kategori') == 'spam' ? 'selected' : '' }}>Spam</option>
                                    <option value="salah-kategori" {{ old('kategori') == 'salah-kategori' ? 'selected' : '' }}>Salah Kategori</option>
                                    <option value="menyesatkan" {{ old('kategori') == 'menyesatkan' ? 'selected' : '' }}>Menyesatkan</option>
                                    <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('kategori')
                                    <p class="text-sm text-[#d4183d] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="space-y-2">
                                <label for="deskripsi" class="block text-sm text-neutral-950">
                                    Deskripsi <span class="text-[#d4183d]">*</span>
                                </label>
                                <textarea
                                    id="deskripsi"
                                    name="deskripsi"
                                    rows="5"
                                    class="w-full px-3 py-2 bg-[#f3f3f5] rounded-lg text-sm border border-neutral-200 shadow-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent placeholder:text-[#717182] {{ $errors->has('deskripsi') ? 'border-[#d4183d]' : '' }}"
                                    placeholder="Jelaskan secara detail pelanggaran yang terjadi...">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi')
                                    <p class="text-sm text-[#d4183d] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="text-[#717182] text-sm">
                                        Minimal 20 karakter. <span id="char-count">0</span>/20
                                    </p>
                                @enderror
                            </div>

                            <!-- Bukti Upload -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <label for="bukti" class="block text-sm text-neutral-950">Bukti (Opsional)</label>
                                    <button type="button" class="text-[#717182] hover:text-neutral-950" title="Upload screenshot atau dokumen yang mendukung laporan Anda">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="file-upload-area" class="border-2 border-dashed border-neutral-200 rounded-lg p-8 text-center transition-colors bg-[#f3f3f5] hover:border-[#009689] cursor-pointer">
                                    <input
                                        type="file"
                                        id="bukti"
                                        name="bukti[]"
                                        multiple
                                        accept="image/png,image/jpeg,application/pdf"
                                        class="hidden"
                                        onchange="handleFileChange(event)"
                                    />
                                    <svg class="w-10 h-10 mx-auto mb-3 text-[#717182]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mb-1 text-neutral-950">Seret & lepas file atau klik untuk pilih</p>
                                    <p class="text-sm text-[#717182]">PNG, JPG, atau PDF (maks. 5MB, 5 file)</p>
                                </div>
                                <div id="file-list" class="space-y-2 hidden"></div>
                                @error('bukti')
                                    <p class="text-sm text-[#d4183d] flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Content Policy Link -->
                            <div class="flex items-center gap-2 p-4 rounded-lg bg-[#f3f3f5] border border-neutral-200">
                                <svg class="w-5 h-5 text-[#009689] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <p class="text-sm text-neutral-950">
                                    Pastikan laporan sesuai dengan
                                    <a href="#" class="text-[#009689] hover:text-teal-700 underline">Kebijakan Konten</a>
                                    kami.
                                </p>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="lg:hidden flex flex-col sm:flex-row gap-3 pt-4">
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="px-6 py-2 border border-neutral-200 text-neutral-950 rounded-lg hover:bg-gray-50 transition-colors w-full sm:w-auto"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="px-6 py-2 bg-gradient-to-r from-[#00bba7] to-[#009966] text-white rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 w-full sm:w-auto"
                                >
                                    <span id="submitText">Kirim Report</span>
                                    <svg id="submitLoader" class="h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Submit Bar - Desktop -->
    <div class="hidden lg:block fixed bottom-0 left-0 right-0 bg-white border-t border-[rgba(0,0,0,0.1)] shadow-lg z-40">
        <div class="max-w-[1200px] mx-auto px-6 py-4">
            <div class="max-w-[640px] mx-auto flex items-center justify-end gap-3">
                <button
                    type="button"
                    onclick="window.history.back()"
                    class="px-6 py-2 border border-neutral-200 text-neutral-950 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    form="reportForm"
                    id="submitBtnDesktop"
                    class="px-6 py-2 bg-gradient-to-r from-[#00bba7] to-[#009966] text-white rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span id="submitTextDesktop">Kirim Report</span>
                    <svg id="submitLoaderDesktop" class="h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Character counter
        const deskripsiField = document.getElementById('deskripsi');
        const charCount = document.getElementById('char-count');
        
        if (deskripsiField && charCount) {
            deskripsiField.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
            charCount.textContent = deskripsiField.value.length;
        }

        // File upload handler
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInput = document.getElementById('bukti');
        const fileList = document.getElementById('file-list');
        let uploadedFiles = [];

        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('border-[#009689]', 'bg-teal-50');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('border-[#009689]', 'bg-teal-50');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-[#009689]', 'bg-teal-50');
            if (e.dataTransfer.files.length > 0) {
                handleFiles(e.dataTransfer.files);
            }
        });

        function handleFileChange(event) {
            if (event.target.files.length > 0) {
                handleFiles(event.target.files);
            }
        }

        function handleFiles(files) {
            const maxFiles = 5;
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'application/pdf'];

            Array.from(files).slice(0, maxFiles - uploadedFiles.length).forEach(file => {
                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name}: Tipe file tidak didukung. Hanya PNG, JPG, dan PDF yang diperbolehkan.`);
                    return;
                }

                if (file.size > maxSize) {
                    alert(`File ${file.name}: Ukuran file terlalu besar. Maksimal 5MB.`);
                    return;
                }

                uploadedFiles.push(file);
                displayFile(file);
            });

            updateFileInput();
        }

        function displayFile(file) {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center gap-3 p-3 rounded-lg border border-neutral-200 bg-white';
            fileItem.dataset.fileName = file.name;

            const isImage = file.type.startsWith('image/');
            const preview = isImage ? `<img src="${URL.createObjectURL(file)}" alt="${file.name}" class="w-12 h-12 object-cover rounded">` : 
                `<div class="w-12 h-12 bg-[#f3f3f5] rounded flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#717182]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>`;

            fileItem.innerHTML = `
                ${preview}
                <div class="flex-1 min-w-0">
                    <p class="truncate text-sm text-neutral-950">${file.name}</p>
                    <p class="text-xs text-[#717182]">${formatFileSize(file.size)}</p>
                </div>
                <button type="button" onclick="removeFile('${file.name}')" class="text-[#717182] hover:text-[#d4183d]">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            fileList.appendChild(fileItem);
            fileList.classList.remove('hidden');
        }

        function removeFile(fileName) {
            uploadedFiles = uploadedFiles.filter(f => f.name !== fileName);
            const fileItem = fileList.querySelector(`[data-file-name="${fileName}"]`);
            if (fileItem) {
                fileItem.remove();
            }
            if (uploadedFiles.length === 0) {
                fileList.classList.add('hidden');
            }
            updateFileInput();
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            uploadedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        // Form submit handler
        const form = document.getElementById('reportForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnDesktop = document.getElementById('submitBtnDesktop');
        const submitText = document.getElementById('submitText');
        const submitTextDesktop = document.getElementById('submitTextDesktop');
        const submitLoader = document.getElementById('submitLoader');
        const submitLoaderDesktop = document.getElementById('submitLoaderDesktop');

        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            if (submitBtnDesktop) submitBtnDesktop.disabled = true;
            submitText.textContent = 'Mengirim...';
            if (submitTextDesktop) submitTextDesktop.textContent = 'Mengirim...';
            submitLoader.classList.remove('hidden');
            if (submitLoaderDesktop) submitLoaderDesktop.classList.remove('hidden');
        });
    </script>
@endpush

