<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Organisasi - NextUse</title>
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
    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[520px]">
            <!-- Title Section -->
            <div class="mb-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-[#00bba7] to-[#009966] rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-neutral-950 text-center mb-3">
                    Login Organisasi/Instansi
                </h1>
                <p class="text-[#717182] text-center">
                    Masuk ke platform NextUse untuk melanjutkan pengelolaan akun organisasi Anda.
                </p>
            </div>

            <!-- Login Form -->
            <form
                id="loginForm"
                class="space-y-6"
                action="{{ route('login.authenticate') }}"
                method="POST"
            >
                @csrf
                @if (session('status'))
                    <div class="p-4 border border-[#009689]/30 bg-teal-50 text-sm text-neutral-950 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="p-4 border border-[#d4183d]/30 bg-red-50 text-sm text-[#d4183d] rounded-lg">
                        <p class="font-medium mb-2">Terjadi kesalahan:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm text-neutral-950">
                        Email Resmi Organisasi
                        <span class="text-[#d4183d] ml-1">*</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="organisasi@email.com"
                        value="{{ old('email') }}"
                        class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('email') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                    />
                    @error('email')
                        <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="10" stroke-width="2" />
                                <path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm text-neutral-950">
                            Password
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <a href="#" class="text-sm text-[#009689] hover:underline">
                            Lupa password organisasi?
                        </a>
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Masukkan password organisasi"
                            class="w-full h-9 px-3 pr-10 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('password') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                        />
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center"
                            aria-label="Tampilkan password"
                        >
                            <svg id="icon-eye-closed-login" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#717182" stroke-width="1.8">
                                <path d="M2 12c2.8-6 8-9 10-9s7.2 3 10 9c-2.8 6-8 9-10 9s-7.2-3-10-9" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 3l18 18" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg id="icon-eye-open-login" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="#717182" stroke-width="1.8">
                                <path d="M2 12c2.8-6 8-9 10-9s7.2 3 10 9c-2.8 6-8 9-10 9s-7.2-3-10-9" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="12" cy="12" r="10" stroke-width="2" />
                                <path d="M12 8v4M12 16h.01" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <input
                            id="rememberMe"
                            type="checkbox"
                            name="rememberMe"
                            class="w-4 h-4 rounded bg-[#f3f3f5] border border-neutral-200 checked:bg-[#009689] checked:border-[#009689] focus:ring-2 focus:ring-[#009689] focus:ring-offset-0 cursor-pointer appearance-none"
                            onchange="updateCheckboxIcon(this)"
                            {{ old('rememberMe') ? 'checked' : '' }}
                        />
                        <svg id="checkbox-check-icon" class="w-4 h-4 text-white absolute inset-0 pointer-events-none hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <label
                        for="rememberMe"
                        class="text-sm text-neutral-950 cursor-pointer select-none"
                    >
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="submitBtn"
                    class="w-full h-9 bg-gradient-to-r from-[#00bba7] to-[#009966] text-white text-sm rounded-[10px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span id="submitText">Masuk</span>
                    <svg id="submitLoader" class="h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-2 text-[#717182]">atau</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center space-y-3">
                    <p class="text-sm text-[#717182]">
                        Belum punya akun organisasi?
                    </p>
                    <a
                        href="{{ route('registrasi') }}"
                        class="block w-full h-9 border border-[#009689] text-[#009689] text-sm rounded-[10px] hover:bg-teal-50 transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Daftar Organisasi/Instansi Baru
                    </a>
                </div>
            </form>

            
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-[rgba(0,0,0,0.1)]">
        <div class="max-w-[1200px] mx-auto px-6 py-6">
            <p class="text-sm text-[#717182] text-center">
                © 2025 NextUse. Platform berbagi barang gratis.
            </p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rememberCheckbox = document.getElementById('rememberMe');
            if (rememberCheckbox) {
                updateCheckboxIcon(rememberCheckbox);
            }

            const form = document.getElementById('loginForm');
            if (form) {
                form.addEventListener('submit', () => {
                    const submitBtn = document.getElementById('submitBtn');
                    const submitText = document.getElementById('submitText');
                    const submitLoader = document.getElementById('submitLoader');

                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                    if (submitText) {
                        submitText.textContent = 'Memproses...';
                    }
                    if (submitLoader) {
                        submitLoader.classList.remove('hidden');
                    }
                });
            }
        });

        function togglePassword() {
            const field = document.getElementById('password');
            const iconOpen = document.getElementById('icon-eye-open-login');
            const iconClosed = document.getElementById('icon-eye-closed-login');
            if (!field) return;
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            if (iconOpen && iconClosed) {
                if (isPassword) {
                    iconOpen.classList.remove('hidden');
                    iconClosed.classList.add('hidden');
                } else {
                    iconOpen.classList.add('hidden');
                    iconClosed.classList.remove('hidden');
                }
            }
        }

        function updateCheckboxIcon(checkbox) {
            const checkIcon = document.getElementById('checkbox-check-icon');
            if (!checkIcon) return;

            if (checkbox.checked) {
                checkIcon.classList.remove('hidden');
            } else {
                checkIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
