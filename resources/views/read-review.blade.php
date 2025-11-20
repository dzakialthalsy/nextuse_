<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan untuk Saya - NextUse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @layer utilities {
            .main-gradient {
                background: linear-gradient(to right, #14b8a6, #10b981);
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50">
    <!-- Header -->
    @include('components.navbar')

    <main class="flex-1 py-8 px-4 sm:px-6">
        <div class="flex justify-center">
            <div class="w-full max-w-[1200px]">
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
                    <h1 class="text-3xl font-semibold mb-2 text-gray-900">
                        @if($isOwnProfile)
                            Ulasan untuk Saya
                        @else
                            Ulasan untuk {{ $organization->organization_name }}
                        @endif
                    </h1>
                    <p class="text-gray-600">Ringkasan reputasi dan umpan balik dari komunitas.</p>
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

                <!-- Summary Section - 2 Cards Side by Side -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Left Card - Overall Rating -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8 shadow-sm">
                        <div class="flex flex-col items-center">
                            <div class="w-36 h-36 rounded-full bg-green-50 flex items-center justify-center mb-4">
                                <span class="text-6xl font-bold text-gray-900">{{ $averageRating }}</span>
                            </div>
                            <div class="flex items-center mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <svg class="w-7 h-7 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @elseif($i - 1 < $averageRating && $i > $averageRating)
                                        <div class="relative">
                                            <svg class="w-7 h-7 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                            <svg class="w-7 h-7 text-yellow-400 fill-current absolute top-0 left-0 overflow-hidden" style="width: 50%;" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                            </svg>
                                        </div>
                                    @else
                                        <svg class="w-7 h-7 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-gray-600 text-base">{{ $totalReviews }} ulasan</p>
                        </div>
                    </div>

                    <!-- Right Card - Rating Distribution -->
                    <div class="bg-white border border-gray-200 rounded-lg p-8 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900 mb-6">Distribusi Rating</h3>
                        <div class="space-y-4">
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $ratingDistribution->get($i) ? $ratingDistribution->get($i)->count : 0;
                                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                @endphp
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-700 w-12">{{ $i }} ⭐</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                                        <div class="bg-gray-900 h-3 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right font-medium">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span class="text-sm font-medium text-yellow-800">Top Giver</span>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-blue-800">Verified User</span>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6 shadow-sm">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input
                                type="text"
                                placeholder="Cari dalam ulasan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            />
                        </div>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option>Semua Ulasan</option>
                            <option>5 Bintang</option>
                            <option>4 Bintang</option>
                            <option>3 Bintang</option>
                            <option>2 Bintang</option>
                            <option>1 Bintang</option>
                        </select>
                        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option>Terbaru</option>
                            <option>Terlama</option>
                            <option>Rating Tertinggi</option>
                            <option>Rating Terendah</option>
                        </select>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-100 to-emerald-100 flex items-center justify-center">
                                            @if($review->show_name && $review->reviewer)
                                                <span class="text-teal-700 font-semibold text-base">{{ substr($review->reviewer->organization_name, 0, 1) }}</span>
                                            @else
                                                <span class="text-gray-500 font-semibold text-base">A</span>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 text-base">
                                                @if($review->show_name && $review->reviewer)
                                                    {{ $review->reviewer->organization_name }}
                                                @else
                                                    Pengguna Anonim
                                                @endif
                                            </h3>
                                            <div class="flex items-center space-x-3 mt-1">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-500">
                                                    @php
                                                        $daysAgo = $review->created_at->diffInDays(now());
                                                        if ($daysAgo == 0) {
                                                            echo 'Hari ini';
                                                        } elseif ($daysAgo == 1) {
                                                            echo 'Kemarin';
                                                        } else {
                                                            echo $daysAgo . ' hari lalu';
                                                        }
                                                    @endphp
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($review->transaction_id)
                                    <p class="text-sm text-gray-600 mb-3">
                                        <span class="font-medium">Tentang:</span> 
                                        <span>Transaksi #{{ $review->transaction_id }}</span>
                                    </p>
                                @endif

                                @if($review->title)
                                    <h4 class="font-bold text-gray-900 mb-3 text-lg">{{ $review->title }}</h4>
                                @endif

                                <p class="text-gray-700 mb-4 whitespace-pre-wrap leading-relaxed">{{ $review->review_text }}</p>

                                @if($review->images && count($review->images) > 0)
                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                        @foreach($review->images as $image)
                                            <div class="relative group">
                                                <img src="{{ asset('storage/' . $image) }}" alt="Review image" class="w-full h-32 object-cover rounded-lg border border-gray-200 cursor-pointer transition-transform group-hover:scale-105" onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Reply Section (Placeholder - bisa ditambahkan jika ada fitur reply) -->
                                {{-- <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center">
                                            <span class="text-teal-700 font-semibold text-xs">A</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600 mb-1">
                                                <span class="font-medium">Balasan Anda</span>
                                                <span class="text-gray-500">Kemarin</span>
                                            </p>
                                            <p class="text-gray-700 text-sm">Terima kasih atas ulasannya! Mohon maaf atas keterlambatan, akan lebih tepat waktu ke depannya.</p>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        @endforeach

                        <!-- Pagination Info -->
                        <div class="text-center text-sm text-gray-600 mt-6">
                            Menampilkan {{ $reviews->firstItem() ?? 0 }} - {{ $reviews->lastItem() ?? 0 }} dari {{ $totalReviews }} ulasan
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $reviews->links() }}
                        </div>
                    @else
                        <div class="bg-white border border-gray-200 rounded-lg p-12 text-center shadow-sm">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Review</h3>
                            <p class="text-gray-600 mb-6">Anda belum memiliki review.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="closeImageModal()">
        <div class="max-w-4xl mx-4 relative">
            <img id="modalImage" src="" alt="Review image" class="max-w-full max-h-screen rounded-lg">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Footer -->
    @include('components.footer')

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>
