@extends('layouts.app')

@section('title', 'Profil Saya - NextUse')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10 space-y-8">
        @php $isAdmin = session('is_admin') === true; @endphp
        @if($isAdmin)
            <div class="rounded-2xl border border-teal-200 bg-teal-50/80 px-4 py-3 text-sm text-teal-800 flex items-center justify-between">
                <span>Profil Admin</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90">Keluar</button>
                </form>
            </div>
        @endif
        <div class="space-y-1">
            <h1 class="text-3xl font-semibold text-slate-900">Profil Saya</h1>
            <p class="text-sm text-slate-500">Kelola informasi pribadi dan pengaturan akun Anda.</p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="flex flex-col gap-6 px-6 py-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        @if ($profile->avatar_url)
                            <img src="{{ $profile->avatar_url }}" alt="{{ $profile->full_name }}"
                                class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-100">
                        @else
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-2xl font-semibold text-white">
                                {{ strtoupper(mb_substr($profile->full_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <div>
                            <p class="text-base font-semibold text-slate-900">{{ $profile->full_name }}</p>
                            <p class="text-sm text-slate-500">{{ $profile->headline ?? '@'.Str::slug($profile->full_name) }}</p>
                        </div>
                        <dl class="mt-3 space-y-2 text-sm text-slate-600">
                            @if ($profile->contact_email)
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-50 text-[10px] text-slate-500">
                                        @
                                    </span>
                                    <div>
                                        <dt class="text-xs text-slate-400">Email</dt>
                                        <dd>{{ $profile->contact_email }}</dd>
                                    </div>
                                </div>
                            @endif
                            @if ($profile->location)
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-50 text-[10px] text-slate-500">
                                        📍
                                    </span>
                                    <div>
                                        <dt class="text-xs text-slate-400">Lokasi</dt>
                                        <dd>{{ $profile->location }}</dd>
                                    </div>
                                </div>
                            @endif
                            @if ($profile->joined_at)
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-50 text-[10px] text-slate-500">
                                        📅
                                    </span>
                                    <div>
                                        <dt class="text-xs text-slate-400">Bergabung</dt>
                                        <dd>{{ $profile->joined_at->translatedFormat('F Y') }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

            @if (\Illuminate\Support\Facades\Schema::hasTable('profiles') && $profile->exists)
                <div class="flex items-start gap-3">
                    <a href="{{ route('profile.edit', $profile) }}"
                        class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Edit Profil
                    </a>
                </div>
            @endif
            </div>

            <div class="border-t border-slate-100 px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Statistik</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs text-slate-500">Items Posted</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['items_posted'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs text-slate-500">Giveaway</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['giveaway'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <p class="text-xs text-slate-500">Trades</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['trades'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </section>

        @unless($isAdmin)
        <section class="space-y-6">
            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Aktivitas Terakhir</h2>
                    <p class="text-xs text-slate-500">Riwayat aktivitas terbaru Anda di NextUse</p>
                </div>
                <ul class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse ($activities as $activity)
                        <li class="flex items-start gap-3 px-6 py-4">
                            <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                            <div class="space-y-1">
                                <p>Memposting item <span class="font-semibold">"{{ $activity->judul }}"</span></p>
                                <p class="text-xs text-slate-400">{{ $activity->created_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-sm text-slate-500">
                            Belum ada aktivitas terbaru. Mulai dengan memposting item pertamamu!
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Lihat Ulasan</h2>
                    <p class="text-xs text-slate-500">Pantau kepercayaan komunitas terhadapmu</p>
                </div>
                <div class="divide-y divide-slate-100 text-sm text-slate-700">
                    <a href="{{ route('review.read', ['organization_id' => $profile->organization_id ?? $profile->id]) }}"
                        class="flex w-full items-center justify-between px-6 py-4 hover:bg-slate-50">
                        <span class="flex items-center gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-xs text-slate-600">
                                ⭐
                            </span>
                            Review masuk
                        </span>
                        <span class="text-xs text-slate-400">Klik untuk melihat halaman review</span>
                    </a>
                    <button type="button" class="flex w-full items-center justify-between px-6 py-4 hover:bg-rose-50" onclick="window.dispatchEvent(new CustomEvent('open-logout-modal'))">
                        <span class="flex items-center gap-3 text-sm font-semibold text-rose-600">
                            🚪 Keluar
                        </span>
                        <span class="text-xs text-rose-400">Sesi saat ini</span>
                    </button>
                </div>
            </div>

            <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-sm font-semibold text-slate-900">Informasi Akun</h2>
                </div>
                <dl class="space-y-3 px-6 py-5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Status Akun</dt>
                        <dd class="text-emerald-600 font-semibold">Aktif</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Verifikasi Email</dt>
                        <dd class="text-emerald-600 font-semibold">Terverifikasi</dd>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <dt class="text-slate-500">Tipe Akun</dt>
                        <dd class="text-slate-700">Gratis</dd>
                    </div>
                </dl>
            </div>
        </section>
        @endunless
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalOverlay = document.getElementById('logout-modal-overlay');
            const cancelButton = document.getElementById('logout-cancel');
            const confirmButton = document.getElementById('logout-confirm');

            window.addEventListener('open-logout-modal', () => {
                modalOverlay.classList.remove('hidden');
                modalOverlay.classList.add('flex');
            });

            cancelButton?.addEventListener('click', () => {
                modalOverlay.classList.add('hidden');
                modalOverlay.classList.remove('flex');
            });

            confirmButton?.addEventListener('click', () => {
                document.getElementById('logout-form').submit();
            });
        });
    </script>

    <div id="logout-modal-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
            <h3 class="text-lg font-semibold text-slate-900">Yakin ingin keluar?</h3>
            <p class="mt-2 text-sm text-slate-500">Sesi kamu akan diakhiri dan perlu login ulang untuk kembali.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button id="logout-cancel" type="button"
                    class="rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button id="logout-confirm" type="button"
                        class="rounded-full bg-rose-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-200 hover:bg-rose-600">
                        Ya, keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endpush


