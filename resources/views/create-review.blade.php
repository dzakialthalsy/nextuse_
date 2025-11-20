<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Penilaian & Ulasan - NextUse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @layer utilities {
            .main-gradient {
                background: linear-gradient(to right, #14b8a6, #10b981);
            }
            .star-filled {
                color: #fbbf24;
                fill: #fbbf24;
            }
            .star-empty {
                color: #d1d5db;
                stroke: #d1d5db;
                fill: none;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    @include('components.navbar')

    <main class="flex-1 py-8 px-4 sm:px-6">
        <div class="max-w-[1200px] mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold mb-2 text-gray-900">Beri Penilaian & Ulasan</h1>
                <p class="text-gray-600">Bagikan pengalaman Anda dengan pengguna ini.</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-center">
                <!-- Main Form -->
                <div class="w-full max-w-[1200px]">
                    <!-- Organization Profile Card -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8 mb-6 shadow-sm">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">{{ $reviewedOrganization->organization_name }}</h3>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-sm text-gray-600">{{ $transactionCount }} transaksi</span>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <span class="text-sm text-gray-900 font-medium">{{ $averageRating }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 border border-red-300 bg-red-50 rounded-lg text-sm text-red-800">
                            <p class="font-medium mb-2">Periksa kembali form Anda:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form action="{{ route('review.create.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="reviewForm">
                        @csrf
                        <input type="hidden" name="reviewed_organization_id" value="{{ $reviewedOrganization->id }}">

                        <!-- Rating -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-900">
                                Rating <span class="text-red-600">*</span>
                            </label>
                            <div class="flex items-center space-x-2" id="rating-container">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="rating-star" data-rating="{{ $i }}">
                                            <svg class="w-8 h-8 star-empty" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="" required>
                                @error('rating')
                                    <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-900">
                                Judul Singkat (Opsional)
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="Contoh: Transaksi sangat lancar"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('title') border-red-500 @enderror"
                            />
                            @error('title')
                                <p class="text-red-600 text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Action Buttons - After Title -->
                        <div class="flex items-center justify-center gap-4 pt-2">
                            <button
                                type="button"
                                onclick="window.history.back()"
                                class="text-gray-700 font-medium py-2.5 px-8 rounded-lg hover:bg-gray-50 border border-gray-300 transition-colors min-w-[590px]"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-2.5 px-8 rounded-lg shadow-sm transition-all hover:shadow-md min-w-[590px]"
                            >
                                Kirim Review
                            </button>
                        </div>

                        <!-- Review Text -->
                        <div class="space-y-2">
                            <label for="review_text" class="block text-sm font-medium text-gray-900">
                                Ulasan <span class="text-red-600">*</span>
                            </label>
                            <textarea
                                id="review_text"
                                name="review_text"
                                rows="6"
                                placeholder="Ceritakan pengalaman Anda dengan pengguna ini. Apa yang membuat transaksi dengan mereka menyenangkan atau perlu dipertimbangkan?"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm resize-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('review_text') border-red-500 @enderror"
                                required
                                oninput="updateCharCount(this)"
                                minlength="20"
                                maxlength="500"
                            >{{ old('review_text') }}</textarea>
                            <p class="text-gray-500 text-sm">
                                <span id="char-count">0</span>/500 karakter • Minimal 20 karakter
                            </p>
                            @error('review_text')
                                <p class="text-red-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-900">
                                Bukti/Gambar (Opsional)
                            </label>
                            <p class="text-sm text-gray-600 mb-2">
                                Maksimal 3 gambar, format JPG/PNG, ukuran maksimal 2MB
                            </p>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                                <input
                                    type="file"
                                    id="images"
                                    name="images[]"
                                    multiple
                                    accept="image/jpeg,image/jpg,image/png"
                                    class="hidden"
                                    onchange="previewImages(this)"
                                />
                                <label for="images" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG (max. 2MB)</p>
                                </label>
                                <div id="image-preview" class="mt-4 grid grid-cols-3 gap-4 hidden"></div>
                            </div>
                            @error('images')
                                <p class="text-red-600 text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            @error('images.*')
                                <p class="text-red-600 text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Privacy Option -->
                        <div class="space-y-2">
                            <div class="flex items-start space-x-2">
                                <input
                                    type="checkbox"
                                    id="show_name"
                                    name="show_name"
                                    value="1"
                                    {{ old('show_name', true) ? 'checked' : '' }}
                                    class="mt-1 w-4 h-4 text-teal-600 focus:ring-teal-500 rounded border-gray-300"
                                />
                                <div class="flex-1">
                                    <label for="show_name" class="text-sm text-gray-900 cursor-pointer flex items-center gap-2">
                                        <span>Tampilkan nama saya di review ini</span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </label>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Jika tidak dicentang, review akan ditampilkan sebagai "Anonim"
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tips and Policy - After Privacy Option -->
                        <div class="space-y-4 pt-4">
                            <!-- Tips for Writing Reviews -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <h3 class="font-semibold text-gray-900">Tips Menulis Review</h3>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Berikan penilaian yang jujur berdasarkan pengalaman Anda</span>
                                    </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Jelaskan secara detail tentang responsivitas, ketepatan waktu, dan kondisi barang</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Hindari bahasa kasar atau menyinggung</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Tambahkan foto jika ada bukti yang mendukung</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Review yang konstruktif membantu komunitas tumbuh</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Review Policy -->
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <h3 class="font-semibold text-gray-900">Kebijakan Ulasan</h3>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-3">
                                        Pastikan review Anda mematuhi pedoman komunitas NextUse. Review yang melanggar akan dihapus.
                                    </p>
                                    <a href="{{ route('syarat-ketentuan') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium inline-flex items-center gap-1">
                                        <span>Baca Selengkapnya</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <script>
        // Rating Star Selection
        let selectedRating = 0;
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('rating-input');

        ratingStars.forEach((star, index) => {
            star.addEventListener('click', function() {
                selectedRating = index + 1;
                ratingInput.value = selectedRating;
                updateStarDisplay(selectedRating);
            });

            star.addEventListener('mouseenter', function() {
                updateStarDisplay(index + 1);
            });
        });

        document.getElementById('rating-container').addEventListener('mouseleave', function() {
            updateStarDisplay(selectedRating);
        });

        function updateStarDisplay(rating) {
            ratingStars.forEach((star, index) => {
                const svg = star.querySelector('svg');
                if (index < rating) {
                    svg.classList.remove('star-empty');
                    svg.classList.add('star-filled');
                    svg.setAttribute('fill', '#fbbf24');
                    svg.setAttribute('stroke', 'none');
                } else {
                    svg.classList.remove('star-filled');
                    svg.classList.add('star-empty');
                    svg.setAttribute('fill', 'none');
                    svg.setAttribute('stroke', '#d1d5db');
                }
            });
        }

        // Character Counter
        function updateCharCount(textarea) {
            const count = textarea.value.length;
            document.getElementById('char-count').textContent = count;
            
            if (count < 20) {
                textarea.classList.add('border-yellow-300');
                textarea.classList.remove('border-red-500');
            } else if (count > 500) {
                textarea.classList.add('border-red-500');
                textarea.classList.remove('border-yellow-300');
            } else {
                textarea.classList.remove('border-yellow-300', 'border-red-500');
            }
        }

        // Image Preview
        function previewImages(input) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                preview.classList.remove('hidden');
                const maxFiles = Math.min(input.files.length, 3);
                
                for (let i = 0; i < maxFiles; i++) {
                    const file = input.files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${i + 1}" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                            <button type="button" onclick="removeImage(${i})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">×</button>
                        `;
                        preview.appendChild(div);
                    };
                    
                    reader.readAsDataURL(file);
                }
            } else {
                preview.classList.add('hidden');
            }
        }

        function removeImage(index) {
            const input = document.getElementById('images');
            const dt = new DataTransfer();
            
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            
            input.files = dt.files;
            previewImages(input);
        }

        // Form Validation
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            const rating = ratingInput.value;
            const reviewText = document.getElementById('review_text').value;
            
            if (!rating || rating < 1 || rating > 5) {
                e.preventDefault();
                alert('Silakan pilih rating (1-5 bintang)');
                return false;
            }
            
            if (reviewText.length < 20) {
                e.preventDefault();
                alert('Ulasan minimal 20 karakter');
                return false;
            }
            
            if (reviewText.length > 500) {
                e.preventDefault();
                alert('Ulasan maksimal 500 karakter');
                return false;
            }
        });

        // Initialize character count
        document.addEventListener('DOMContentLoaded', function() {
            const reviewText = document.getElementById('review_text');
            if (reviewText.value) {
                updateCharCount(reviewText);
            }
        });
    </script>
</body>
</html>
