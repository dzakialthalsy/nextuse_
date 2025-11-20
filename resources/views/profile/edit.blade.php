@extends('layouts.app')

@section('title', 'Edit Profil - NextUse')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">
        <nav class="text-xs text-slate-400">
            <a href="{{ route('profile.index') }}" class="hover:text-slate-600">Profil</a>
            <span class="mx-1">/</span>
            <span class="text-slate-600">Edit Profil</span>
        </nav>

        <div class="space-y-1">
            <h1 class="text-2xl font-semibold text-slate-900">Edit Profil</h1>
            <p class="text-sm text-slate-500">Perbarui informasi akun Anda.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-100 bg-rose-50/80 px-4 py-3 text-sm text-rose-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <form action="{{ route('profile.update', $profile) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <section class="space-y-4">
                    <div class="space-y-2">
                        <h2 class="text-sm font-semibold text-slate-900">Foto Profil</h2>
                        <p class="text-xs text-slate-500">Seret &amp; lepas atau klik untuk pilih (JPG/PNG maks. 2MB).</p>
                    </div>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="relative">
                            <img src="{{ $profile->avatar_url ?? 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&w=200&q=80' }}"
                                alt="Avatar preview"
                                class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-200">
                            <span class="absolute -bottom-1 -right-1 rounded-full bg-emerald-500 px-2 py-1 text-[10px] font-semibold text-white">Baru</span>
                        </div>
                        <label for="avatar"
                            class="flex-1 cursor-pointer rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-5 text-center text-xs text-slate-500 hover:bg-slate-100">
                            <p class="font-medium text-slate-700">Klik untuk memilih foto</p>
                            <p>JPG atau PNG (maks. 2MB)</p>
                            <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*">
                        </label>
                    </div>
                </section>

                <section class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-600">Nama Lengkap <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-600">Username</label>
                            <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                placeholder="@namapengguna">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Bio</label>
                        <textarea name="bio" rows="4"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            placeholder="Ceritakan sedikit tentang diri Anda...">{{ old('bio', $profile->bio) }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location', $profile->location) }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            placeholder="Jakarta, Indonesia">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Nomor WhatsApp (Opsional)</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $profile->contact_phone) }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                            placeholder="+628xxxxxxx">
                        <p class="text-xs text-slate-400">Untuk komunikasi dengan pengguna lain.</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-900">Tautan Sosial (Opsional)</h2>
                                <p class="text-xs text-slate-500">Maksimal beberapa tautan sosial. Kosongkan jika tidak diperlukan.</p>
                            </div>
                        </div>

                        @php
                            $socialLinks = collect($profile->social_links ?? []);
                        @endphp

                        <div class="space-y-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4">
                            <div class="grid gap-3 md:grid-cols-[120px_minmax(0,1fr)]">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-600">Label</label>
                                    <input type="text" value="Instagram" disabled
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs text-slate-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-600">URL</label>
                                    <input type="url" name="instagram_url"
                                        value="{{ old('instagram_url', $socialLinks['instagram'] ?? '') }}"
                                        class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                        placeholder="https://instagram.com/username">
                                </div>
                            </div>
                            <div class="grid gap-3 md:grid-cols-[120px_minmax(0,1fr)]">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-600">Label</label>
                                    <input type="text" value="TikTok" disabled
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs text-slate-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-600">URL</label>
                                    <input type="url" name="tiktok_url"
                                        value="{{ old('tiktok_url', $socialLinks['tiktok'] ?? '') }}"
                                        class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                        placeholder="https://tiktok.com/@username">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
                    <a href="{{ route('profile.index') }}"
                        class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


