<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Organisasi - NextUse</title>
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
<body class="min-h-screen bg-gradient-to-br from-teal-50 via-green-50 to-emerald-50">
    <!-- Main Content -->
    <div class="max-w-[1200px] mx-auto px-6 py-12">
        <div class="grid lg:grid-cols-[560px_1fr] gap-12">
            <!-- Form Section -->
            <div>
                <!-- Title -->
                <div class="mb-8">
                    <h1 class="text-neutral-950 text-center mb-3">Registrasi Organisasi/Instansi</h1>
                    <p class="text-[#717182] text-center">
                        Daftarkan organisasi atau institusi Anda untuk berbagi atau menukar barang secara gratis di platform NextUse.
                    </p>
                </div>

                <!-- Form -->
            <form
                id="registrationForm"
                class="space-y-6"
                action="{{ route('registrasi.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                    @if ($errors->any())
                        <div class="p-4 border border-[#d4183d]/30 bg-red-50 rounded-lg text-sm text-[#d4183d] leading-relaxed">
                            <p class="font-medium mb-2">Periksa kembali data yang diisi:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- Nama Organisasi -->
                    <div class="space-y-2">
                        <label for="organizationName" class="block text-sm text-neutral-950">
                            Nama Organisasi/Instansi
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <input
                            id="organizationName"
                            type="text"
                            name="organizationName"
                            placeholder="Contoh: Yayasan Peduli Sesama"
                        value="{{ old('organizationName') }}"
                        class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('organizationName') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                        />
                    @error('organizationName')
                        <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                    </div>

                    <!-- Jenis Organisasi -->
                    <div class="space-y-2">
                        <label for="organizationType" class="block text-sm text-neutral-950">
                            Jenis Organisasi
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <select
                            id="organizationType"
                            name="organizationType"
                        class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm border {{ $errors->has('organizationType') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent {{ old('organizationType') ? 'text-neutral-950' : 'text-[#717182]' }}"
                        >
                        <option value="">Pilih jenis organisasi</option>
                        <option value="yayasan" {{ old('organizationType') === 'yayasan' ? 'selected' : '' }}>Yayasan</option>
                        <option value="kampus" {{ old('organizationType') === 'kampus' ? 'selected' : '' }}>Kampus</option>
                        <option value="sekolah" {{ old('organizationType') === 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                        <option value="pemerintah" {{ old('organizationType') === 'pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                        <option value="komunitas" {{ old('organizationType') === 'komunitas' ? 'selected' : '' }}>Komunitas</option>
                        <option value="perusahaan-sosial" {{ old('organizationType') === 'perusahaan-sosial' ? 'selected' : '' }}>Perusahaan Sosial</option>
                        <option value="lainnya" {{ old('organizationType') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    @error('organizationType')
                        <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                    </div>

                    <!-- Nomor Induk Organisasi -->
                    <div class="space-y-2">
                        <label for="organizationId" class="block text-sm text-neutral-950">
                            Nomor Induk Organisasi atau NIB
                            <span class="text-[#717182] text-xs ml-2">(Opsional)</span>
                        </label>
                        <input
                            id="organizationId"
                            type="text"
                            name="organizationId"
                            placeholder="Contoh: 1234567890123456"
                    value="{{ old('organizationId') }}"
                    class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border border-neutral-200 shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                        />
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm text-neutral-950">
                            Email Resmi Organisasi
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="contoh@organisasi.com"
                    value="{{ old('email') }}"
                    class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('email') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                        />
                @error('email')
                    <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <p class="text-sm {{ $errors->has('email') ? 'hidden' : 'text-[#717182]' }}">Pastikan email unik dan valid</p>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="space-y-2">
                        <label for="phone" class="block text-sm text-neutral-950">
                            Nomor Telepon/Kontak PIC
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            placeholder="08xx-xxxx-xxxx"
                    value="{{ old('phone') }}"
                    class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('phone') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                        />
                @error('phone')
                    <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                    </div>

                    <!-- Nama Penanggung Jawab -->
                    <div class="space-y-2">
                        <label for="contactPerson" class="block text-sm text-neutral-950">
                            Nama Penanggung Jawab/Contact Person
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <input
                            id="contactPerson"
                            type="text"
                            name="contactPerson"
                            placeholder="Contoh: Budi Santoso"
                    value="{{ old('contactPerson') }}"
                    class="w-full h-9 px-3 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('contactPerson') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                        />
                @error('contactPerson')
                    <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm text-neutral-950">
                            Password
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Buat password yang kuat"
                                class="w-full h-9 px-3 pr-10 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('password') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('password')"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center"
                            >
                                <svg id="eye-password" class="w-5 h-5" fill="none" viewBox="0 0 20 20" stroke="#717182">
                                    <g>
                                        <path
                                            d="M0.885421 6.95615C0.815971 6.76906 0.815971 6.56325 0.885421 6.37615C1.56184 4.73603 2.71002 3.33369 4.1844 2.3469C5.65878 1.36012 7.39296 0.833333 9.16709 0.833333C10.9412 0.833333 12.6754 1.36012 14.1498 2.3469C15.6242 3.33369 16.7723 4.73603 17.4488 6.37615C17.5182 6.56325 17.5182 6.76906 17.4488 6.95615C16.7723 8.59627 15.6242 9.99862 14.1498 10.9854C12.6754 11.9722 10.9412 12.499 9.16709 12.499C7.39296 12.499 5.65878 11.9722 4.1844 10.9854C2.71002 9.99862 1.56184 8.59627 0.885421 6.95615Z"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.66667"
                                        />
                                        <path
                                            id="eye-slash-password"
                                            d="M3.33333 5.83333C4.71405 5.83333 5.83333 4.71405 5.83333 3.33333C5.83333 1.95262 4.71405 0.833333 3.33333 0.833333C1.95262 0.833333 0.833333 1.95262 0.833333 3.33333C0.833333 4.71405 1.95262 5.83333 3.33333 5.83333Z"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.66667"
                                        />
                                    </g>
                                </svg>
                            </button>
                        </div>
                        <div id="password-strength" class="hidden space-y-1">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="password-strength-bar" class="h-full transition-all bg-[#d4183d]" style="width: 0%"></div>
                                </div>
                                <span id="password-strength-label" class="text-xs text-[#717182]"></span>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                        <p id="hint-password" class="text-sm text-[#717182] {{ $errors->has('password') ? 'hidden' : '' }}">Minimal 8 karakter dengan kombinasi huruf, angka, dan simbol</p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="space-y-2">
                        <label for="confirmPassword" class="block text-sm text-neutral-950">
                            Konfirmasi Password
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="confirmPassword"
                                type="password"
                                name="confirmPassword"
                                placeholder="Ulangi password Anda"
                                class="w-full h-9 px-3 pr-10 py-1 bg-[#f3f3f5] rounded-[10px] text-sm placeholder:text-[#717182] border {{ $errors->has('confirmPassword') ? 'border-[#d4183d]' : 'border-neutral-200' }} shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] focus:outline-none focus:ring-2 focus:ring-[#009689] focus:border-transparent"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('confirmPassword')"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center"
                            >
                                <svg id="eye-confirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 20 20" stroke="#717182">
                                    <g>
                                        <path
                                            d="M0.885421 6.95615C0.815971 6.76906 0.815971 6.56325 0.885421 6.37615C1.56184 4.73603 2.71002 3.33369 4.1844 2.3469C5.65878 1.36012 7.39296 0.833333 9.16709 0.833333C10.9412 0.833333 12.6754 1.36012 14.1498 2.3469C15.6242 3.33369 16.7723 4.73603 17.4488 6.37615C17.5182 6.56325 17.5182 6.76906 17.4488 6.95615C16.7723 8.59627 15.6242 9.99862 14.1498 10.9854C12.6754 11.9722 10.9412 12.499 9.16709 12.499C7.39296 12.499 5.65878 11.9722 4.1844 10.9854C2.71002 9.99862 1.56184 8.59627 0.885421 6.95615Z"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.66667"
                                        />
                                        <path
                                            id="eye-slash-confirmPassword"
                                            d="M3.33333 5.83333C4.71405 5.83333 5.83333 4.71405 5.83333 3.33333C5.83333 1.95262 4.71405 0.833333 3.33333 0.833333C1.95262 0.833333 0.833333 1.95262 0.833333 3.33333C0.833333 4.71405 1.95262 5.83333 3.33333 5.83333Z"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.66667"
                                        />
                                    </g>
                                </svg>
                            </button>
                        </div>
                        @error('confirmPassword')
                            <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Upload Document -->
                    <div class="space-y-2">
                        <label for="document" class="block text-sm text-neutral-950">
                            Upload Surat Penugasan/Surat Kuasa
                            <span class="text-[#d4183d] ml-1">*</span>
                        </label>
                        <div id="document-upload-area" class="border-2 border-dashed {{ $errors->has('document') ? 'border-[#d4183d] bg-red-50' : 'border-[#009689] bg-teal-50' }} rounded-[10px] p-6 transition-colors hover:border-teal-600">
                            <input
                                id="document"
                                type="file"
                                name="document"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                            />
                            <label
                                for="document"
                                class="flex flex-col items-center gap-3 cursor-pointer"
                            >
                                <div class="p-3 bg-white rounded-full shadow-sm border border-[#009689]">
                                    <svg class="h-6 w-6 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p id="document-file-name" class="text-sm text-neutral-950">
                                        Klik untuk pilih file
                                    </p>
                                    <p class="text-xs text-[#717182] mt-1">
                                        PDF, JPG, atau PNG (Maks. 5MB)
                                    </p>
                                </div>
                            </label>
                        </div>
                        @error('document')
                            <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Agreement Checkbox -->
                    <div class="space-y-2">
                        <div class="flex items-start gap-3">
                            <div class="relative pt-0.5">
                                <input
                                    id="agreement"
                                    type="checkbox"
                                    name="agreement"
                                    class="w-4 h-4 rounded bg-[#f3f3f5] border {{ $errors->has('agreement') ? 'border-[#d4183d]' : 'border-neutral-200' }} checked:bg-[#009689] checked:border-[#009689] focus:ring-2 focus:ring-[#009689] focus:ring-offset-0 cursor-pointer"
                                    {{ old('agreement') ? 'checked' : '' }}
                                />
                            </div>
                            <label
                                for="agreement"
                                class="text-sm text-neutral-950 cursor-pointer leading-5 flex-1"
                            >
                                Saya menyatakan data organisasi valid & setuju dengan
                                <a href="{{ route('syarat-ketentuan') }}" target="_blank" class="text-[#009689] underline hover:text-teal-700">
                                    Syarat & Ketentuan
                                </a>
                                NextUse
                                <span class="text-[#d4183d] ml-1">*</span>
                            </label>
                        </div>
                        @error('agreement')
                            <p class="text-sm text-[#d4183d] flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            id="submitBtn"
                            class="w-full h-9 bg-gradient-to-r from-[#00bba7] to-[#009966] text-white text-sm rounded-[10px] shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1),0px_1px_2px_-1px_rgba(0,0,0,0.1)] hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <span id="submitText">Daftarkan Organisasi</span>
                            <svg id="submitLoader" class="h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Secondary Actions -->
                    <div class="space-y-2 pt-4">
                        <p class="text-sm text-[#717182] text-center">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-[#009689] hover:underline">
                                Masuk di sini
                            </a>
                        </p>
                        <p class="text-sm text-center">
                            <a href="{{ route('syarat-ketentuan') }}" class="text-[#717182] underline hover:text-neutral-950">
                                Lihat S&K
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Sticky Sidebar -->
            <div class="hidden lg:block">
                <div class="sticky top-8 space-y-6">
                    <!-- Info Card -->
                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 shadow-sm">
                        <h3 class="flex items-center gap-2 mb-4 text-neutral-950">
                            <svg class="h-5 w-5 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Keuntungan Bergabung
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm border border-teal-200">
                                    <svg class="h-4 w-4 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <p class="text-sm text-neutral-950 leading-relaxed">
                                    Distribusi barang gratis untuk organisasi yang membutuhkan
                                </p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm border border-teal-200">
                                    <svg class="h-4 w-4 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-neutral-950 leading-relaxed">
                                    Jejaring organisasi sosial se-Indonesia
                                </p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm border border-teal-200">
                                    <svg class="h-4 w-4 text-[#009689]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-neutral-950 leading-relaxed">
                                    Platform aman dan terverifikasi
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Requirements Card -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 shadow-sm">
                        <h3 class="flex items-center gap-2 mb-4 text-neutral-950">
                            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Dokumen yang Diperlukan
                        </h3>
                        <ul class="space-y-2.5 text-sm text-neutral-950">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                Surat penugasan/kuasa resmi
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                Email resmi organisasi (aktif)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                Data penanggung jawab
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                Nomor kontak yang dapat dihubungi
                            </li>
                        </ul>
                    </div>

                    <!-- Help Card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 shadow-sm">
                        <p class="text-sm text-neutral-950 mb-3">
                            Butuh bantuan? Hubungi tim NextUse:
                        </p>
                        <div class="space-y-2">
                            <a href="mailto:support@nextuse.id" class="flex items-center gap-2 text-sm text-[#009689] hover:underline">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                support@nextuse.id
                            </a>
                            <a href="tel:+6281234567890" class="flex items-center gap-2 text-sm text-[#009689] hover:underline">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                +62 812-3456-7890
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-[rgba(0,0,0,0.1)] mt-16">
        <div class="max-w-[1200px] mx-auto px-6 py-6">
            <p class="text-sm text-[#717182] text-center">
                © 2025 NextUse. Platform berbagi dan barter barang gratis.
            </p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const strengthContainer = document.getElementById('password-strength');
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthLabel = document.getElementById('password-strength-label');

            if (passwordInput && strengthContainer && strengthBar && strengthLabel) {
                const updateStrength = (value) => {
                    const strength = calculatePasswordStrength(value);
                    if (value && strength > 0) {
                        strengthContainer.classList.remove('hidden');
                        strengthBar.style.width = `${strength}%`;
                        strengthBar.style.backgroundColor = getPasswordStrengthColor(strength);
                        strengthLabel.textContent = getPasswordStrengthLabel(strength);
                    } else {
                        strengthContainer.classList.add('hidden');
                    }
                };

                passwordInput.addEventListener('input', (event) => {
                    updateStrength(event.target.value);
                });

                if (passwordInput.value) {
                    updateStrength(passwordInput.value);
                }
            }

            const documentInput = document.getElementById('document');
            if (documentInput) {
                documentInput.addEventListener('change', handleFileChange);
            }

            const form = document.getElementById('registrationForm');
            if (form) {
                form.addEventListener('submit', () => {
                    const submitBtn = document.getElementById('submitBtn');
                    const submitText = document.getElementById('submitText');
                    const submitLoader = document.getElementById('submitLoader');

                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                    if (submitText) {
                        submitText.textContent = 'Mendaftarkan...';
                    }
                    if (submitLoader) {
                        submitLoader.classList.remove('hidden');
                    }
                });
            }
        });

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return;

            const eyeSlash = document.getElementById(`eye-slash-${fieldId}`);
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';

            if (eyeSlash) {
                eyeSlash.style.display = isPassword ? 'none' : 'block';
            }
        }

        function calculatePasswordStrength(pass) {
            if (!pass) return 0;
            let strength = 0;
            if (pass.length >= 8) strength += 25;
            if (pass.length >= 12) strength += 25;
            if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) strength += 25;
            if (/[0-9]/.test(pass)) strength += 12.5;
            if (/[^a-zA-Z0-9]/.test(pass)) strength += 12.5;
            return Math.min(strength, 100);
        }

        function getPasswordStrengthLabel(strength) {
            if (strength === 0) return '';
            if (strength < 50) return 'Lemah';
            if (strength < 75) return 'Sedang';
            return 'Kuat';
        }

        function getPasswordStrengthColor(strength) {
            if (strength < 50) return '#d4183d';
            if (strength < 75) return '#eab308';
            return '#009689';
        }

        function handleFileChange(event) {
            const file = event.target.files?.[0];
            const fileNameEl = document.getElementById('document-file-name');
            const uploadArea = document.getElementById('document-upload-area');

            if (!fileNameEl || !uploadArea) {
                return;
            }

            if (!file) {
                fileNameEl.textContent = 'Klik untuk pilih file';
                uploadArea.classList.remove('border-[#d4183d]', 'bg-red-50');
                uploadArea.classList.add('border-[#009689]', 'bg-teal-50');
                return;
            }

            const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 5 * 1024 * 1024;

            if (!validTypes.includes(file.type)) {
                alert('Format file tidak valid. Gunakan PDF, JPG, atau PNG.');
                event.target.value = '';
                fileNameEl.textContent = 'Klik untuk pilih file';
                uploadArea.classList.remove('border-[#d4183d]', 'bg-red-50');
                uploadArea.classList.add('border-[#009689]', 'bg-teal-50');
                return;
            }

            if (file.size > maxSize) {
                alert('Ukuran file maksimal 5MB.');
                event.target.value = '';
                fileNameEl.textContent = 'Klik untuk pilih file';
                uploadArea.classList.remove('border-[#d4183d]', 'bg-red-50');
                uploadArea.classList.add('border-[#009689]', 'bg-teal-50');
                return;
            }

            fileNameEl.textContent = file.name;
            uploadArea.classList.remove('border-[#d4183d]', 'bg-red-50');
            uploadArea.classList.add('border-[#009689]', 'bg-teal-50');
        }
    </script>
</body>
</html>

