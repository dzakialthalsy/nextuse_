@extends('layouts.app')

@section('title', 'Profil - NextUse')

@section('content')
    @php
        $skills = collect($profile->skills ?? []);
        $categories = collect($profile->favorite_categories ?? []);
        $socialLinks = collect($profile->social_links ?? []);
        $joinedDate = optional($profile->joined_at)->translatedFormat('F Y');
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-10 space-y-8">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-100 bg-rose-50/80 px-4 py-3 text-sm text-rose-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="overflow-hidden rounded-3xl bg-white shadow-xl shadow-emerald-50 ring-1 ring-slate-100">
            <div class="relative h-56 w-full bg-slate-100">
                @if ($profile->cover_url)
                    <img src="{{ $profile->cover_url }}" alt="Cover" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent"></div>
                <div class="absolute bottom-6 left-6 flex items-center gap-4 text-white">
                    <img src="{{ $profile->avatar_url ?? 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80' }}"
                        alt="Avatar" class="h-20 w-20 rounded-2xl border-4 border-white object-cover shadow-lg">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-emerald-200">Profil Organisasi</p>
                        <h1 class="text-3xl font-semibold">{{ $profile->full_name }}</h1>
                        <p class="text-sm text-emerald-50">{{ $profile->headline }}</p>
                    </div>
                </div>
                <div class="absolute top-6 right-6 flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-white/90 px-4 py-2 text-xs font-semibold text-slate-700">
                        {{ $profile->availability_status }}
                    </span>
                    <a href="{{ route('profile.edit', $profile) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-emerald-600 hover:bg-white shadow">
                        Edit Profil
                    </a>
                    <form action="{{ route('profile.destroy', $profile) }}" method="POST"
                        onsubmit="return confirm('Reset profil ke data default?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-500 hover:bg-rose-100">
                            Reset
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid gap-6 p-6 lg:grid-cols-[2fr_1fr]">
                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-100 p-5">
                        <p class="text-sm text-slate-500">{{ $profile->bio }}</p>
                        <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-500">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25a7.5 7.5 0 1115 0z" />
                                </svg>
                                {{ $profile->location }}
                            </span>
                            @if ($joinedDate)
                                <span>Bergabung {{ $joinedDate }}</span>
                            @endif
                            <span>Login terakhir sebagai {{ $organizationName }}</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Performa</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Rating</p>
                                <p class="text-3xl font-semibold text-slate-900">{{ number_format($profile->rating, 1) }}</p>
                                <p class="text-xs text-slate-400">Berdasarkan ulasan komunitas</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Transaksi Selesai</p>
                                <p class="text-3xl font-semibold text-slate-900">{{ $profile->completed_deals }}</p>
                                <p class="text-xs text-slate-400">Dalam 12 bulan terakhir</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Followers</p>
                                <p class="text-3xl font-semibold text-slate-900">{{ number_format($profile->followers_count) }}</p>
                                <p class="text-xs text-slate-400">Komunitas aktif</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Response Rate</p>
                                <p class="text-3xl font-semibold text-slate-900">{{ $profile->response_rate }}%</p>
                                <p class="text-xs text-slate-400">{{ $profile->response_time }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Keahlian</p>
                            <p class="text-xs text-slate-400">Terupdate otomatis dari riwayat kurasi</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @forelse ($skills as $skill)
                                <span class="rounded-full bg-emerald-50 px-4 py-1 text-sm font-medium text-emerald-600">{{ $skill }}</span>
                            @empty
                                <span class="text-sm text-slate-400">Belum ada data keahlian.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 p-5 space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Kategori Favorit</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @forelse ($categories as $category)
                                <div class="rounded-2xl border border-slate-100 px-4 py-3 flex items-center justify-between">
                                    <span class="font-semibold text-slate-700">{{ $category }}</span>
                                    <span class="text-xs text-slate-400">+12 listing</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Tambahkan kategori favorit melalui mode edit.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <aside class="space-y-4">
                    <div class="rounded-2xl border border-slate-100 p-5 space-y-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Kontak</p>
                        <div class="space-y-2 text-sm text-slate-600">
                            <p class="font-semibold text-slate-800">{{ $profile->contact_email }}</p>
                            <p>{{ $profile->contact_phone }}</p>
                            <a href="{{ $profile->portfolio_url }}" target="_blank" class="text-emerald-600 hover:underline">
                                Portfolio & Koleksi
                            </a>
                        </div>
                        <div class="flex gap-3">
                            @foreach ($socialLinks as $platform => $url)
                                <a href="{{ $url }}" target="_blank"
                                    class="rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500 hover:bg-emerald-50 hover:text-emerald-600">
                                    {{ $platform }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 p-5 space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-500">Highlight</p>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li>• Menang NextUse Award 2024</li>
                            <li>• Kolaborasi live shopping dengan 20+ creator</li>
                            <li>• Menghidupkan kembali 3,2 ton barang bekas</li>
                        </ul>
                        <div class="rounded-2xl bg-emerald-50/80 p-4 text-sm text-emerald-700">
                            “Kami ingin pencari barang bekas merasa percaya diri sejak chat pertama.”
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        @isset($editProfile)
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-lg shadow-emerald-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-500 font-semibold">Mode Edit</p>
                        <h2 class="text-2xl font-semibold text-slate-900">Perbarui Profil</h2>
                    </div>
                    <a href="{{ route('profile.index') }}" class="text-sm font-semibold text-slate-500 hover:text-emerald-600">Batal</a>
                </div>
                <form action="{{ route('profile.update', $editProfile) }}" method="POST" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nama lengkap</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $editProfile->full_name) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Headline</label>
                            <input type="text" name="headline" value="{{ old('headline', $editProfile->headline) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Deskripsi</label>
                        <textarea name="bio" rows="4"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">{{ old('bio', $editProfile->bio) }}</textarea>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Lokasi</label>
                            <input type="text" name="location" value="{{ old('location', $editProfile->location) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</label>
                            <input type="text" name="availability_status" value="{{ old('availability_status', $editProfile->availability_status) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tanggal bergabung</label>
                            <input type="date" name="joined_at" value="{{ old('joined_at', optional($editProfile->joined_at)->format('Y-m-d')) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Rating</label>
                            <input type="number" step="0.1" max="5" name="rating" value="{{ old('rating', $editProfile->rating) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Transaksi</label>
                            <input type="number" min="0" name="completed_deals" value="{{ old('completed_deals', $editProfile->completed_deals) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Followers</label>
                            <input type="number" min="0" name="followers_count" value="{{ old('followers_count', $editProfile->followers_count) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Following</label>
                            <input type="number" min="0" name="following_count" value="{{ old('following_count', $editProfile->following_count) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Skills (pisahkan dengan koma)</label>
                            <input type="text" name="skills_text" value="{{ old('skills_text', $skills->implode(', ')) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Kategori favorit</label>
                            <input type="text" name="categories_text" value="{{ old('categories_text', $categories->implode(', ')) }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $editProfile->contact_email) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Telepon</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $editProfile->contact_phone) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Avatar URL</label>
                            <input type="url" name="avatar_url" value="{{ old('avatar_url', $editProfile->avatar_url) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cover URL</label>
                            <input type="url" name="cover_url" value="{{ old('cover_url', $editProfile->cover_url) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Portfolio URL</label>
                            <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $editProfile->portfolio_url) }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Instagram</label>
                            <input type="url" name="instagram_url" value="{{ old('instagram_url', $socialLinks['instagram'] ?? '') }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">TikTok</label>
                            <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $socialLinks['tiktok'] ?? '') }}"
                                class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('profile.index') }}" class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500">Batal</a>
                        <button type="submit" class="rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        @endisset
    </div>
@endsection

