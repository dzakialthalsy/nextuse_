@extends('layouts.app')

@section('title', 'Dukung NextUse')

@section('content')
<div class="min-h-screen bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-3 mb-4">
                <svg class="w-8 h-8 text-teal-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
                <h1 class="text-3xl font-bold text-gray-900">Dukung NextUse</h1>
            </div>
            <p class="text-gray-600 text-base">
                Scan QRIS untuk berdonasi dan membantu platform terus berkembang.
            </p>
        </div>

        <!-- QRIS Code Section -->
        <div class="flex justify-center mb-8">
            <div class="bg-white border-2 border-gray-200 rounded-lg p-6 shadow-lg max-w-md w-full">
                <!-- QRIS Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="text-xs text-gray-700">
                        <div class="font-semibold">GRIS QR Code</div>
                        <div>Standar Pembayaran Nasional</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="bg-red-600 text-white text-xs px-2 py-1 rounded font-semibold">GPN</div>
                    </div>
                </div>

                <!-- Merchant Info -->
                <div class="mb-4 text-sm text-gray-800">
                    <div class="font-semibold">RAIHANNN, LWKWR</div>
                    <div class="text-xs text-gray-600">NMID: ID1025432846992</div>
                    <div class="text-xs text-gray-600">A01</div>
                </div>

                <!-- QR Code Placeholder -->
                <div class="flex justify-center mb-4">
                    <div class="w-64 h-64 bg-gray-100 border-2 border-gray-300 rounded-lg flex items-center justify-center">
                        <div class="text-center text-gray-500 text-sm">
                            <svg class="w-32 h-32 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <div>QRIS Code</div>
                            <div class="text-xs mt-1">Scan dengan aplikasi pembayaran</div>
                        </div>
                    </div>
                </div>

                <!-- QRIS Footer -->
                <div class="text-center text-xs text-gray-700 space-y-1">
                    <div class="font-semibold">SATU QRIS UNTUK SEMUA</div>
                    <div class="text-gray-600">Cek aplikasi penyelenggara di: www.qris.id</div>
                    <div class="text-gray-500 mt-2 pt-2 border-t border-gray-200">
                        Dicetak oleh: 93600914<br>
                        Versi cetak: V0.0.2025.09.05
                    </div>
                </div>
            </div>
        </div>

        <!-- QRIS Information Box -->
        <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 mb-8 max-w-2xl mx-auto">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-teal-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-sm text-gray-700 leading-relaxed">
                    QRIS adalah metode pembayaran universal yang didukung oleh semua bank dan e-wallet di Indonesia. Aman, cepat, dan langsung terverifikasi secara otomatis.
                </p>
            </div>
        </div>

        <!-- Optional Message Section -->
        <div class="max-w-2xl mx-auto mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pesan (Opsional)</h2>
            
            <form action="{{ route('dukung-nextuse.store') }}" method="POST" id="donationForm">
                @csrf
                
                <!-- Message Input -->
                <div class="mb-6">
                    <textarea 
                        id="pesan" 
                        name="pesan" 
                        rows="6" 
                        maxlength="200"
                        placeholder="Tulis pesan dukungan Anda..."
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none text-gray-900 placeholder-gray-400"
                    >{{ old('pesan') }}</textarea>
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-sm text-gray-500">
                            <span id="charCount">0</span>/200 karakter
                        </div>
                        @error('pesan')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Anonymous Donation Toggle -->
                <div class="mb-6">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div>
                            <div class="text-base font-medium text-gray-900 mb-1">Donasi Anonim</div>
                            <div class="text-sm text-gray-500">Nama Anda tidak akan ditampilkan</div>
                        </div>
                        <div class="relative inline-block w-11 h-6">
                            <input 
                                type="checkbox" 
                                id="is_anonim" 
                                name="is_anonim" 
                                value="1"
                                {{ old('is_anonim') ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-teal-500 peer-checked:bg-teal-500 transition-colors duration-200 ease-in-out">
                                <div class="absolute left-[2px] top-[2px] bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform duration-200 ease-in-out"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="text-sm text-red-800 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-teal-500 hover:bg-teal-600 text-white font-medium rounded-lg transition duration-150 shadow-md hover:shadow-lg"
                    >
                        Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-6 border-t border-gray-200 mt-12">
        <p class="text-sm text-gray-600">
            © 2025 NextUse. Terima kasih atas dukungan Anda. 🙏
        </p>
    </footer>
</div>

<script>
    // Character counter
    const pesanTextarea = document.getElementById('pesan');
    const charCount = document.getElementById('charCount');

    if (pesanTextarea && charCount) {
        // Update on load
        charCount.textContent = pesanTextarea.value.length;

        // Update on input
        pesanTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }

    // Toggle switch functionality
    const toggleSwitch = document.getElementById('is_anonim');
    const toggleSlider = document.querySelector('#is_anonim + div');
    
    if (toggleSwitch && toggleSlider) {
        const updateToggle = () => {
            const slider = toggleSlider.querySelector('div');
            if (toggleSwitch.checked) {
                slider.style.transform = 'translateX(1.25rem)';
            } else {
                slider.style.transform = 'translateX(0)';
            }
        };
        
        // Initialize
        updateToggle();
        
        // Update on change
        toggleSwitch.addEventListener('change', updateToggle);
    }
</script>
@endsection

