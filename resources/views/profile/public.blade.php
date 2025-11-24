@extends('layouts.app')

@section('title', $profile->full_name . ' - NextUse Profile')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10 space-y-8">
        <div class="flex flex-col gap-6 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        @if ($profile->avatar_url)
                            <img src="{{ $profile->avatar_url }}" alt="{{ $profile->full_name }}"
                                class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-100">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-2xl font-semibold text-white">
                                {{ strtoupper(mb_substr($profile->full_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-emerald-500 font-semibold">Profil Donatur</p>
                        <h1 class="text-3xl font-bold text-slate-900">{{ $profile->full_name }}</h1>
                        <p class="text-sm text-slate-500">{{ $profile->headline }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $organization->organization_type ?? 'Organisasi' }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600">
                        {{ $profile->availability_status }}
                    </span>
                    <a href="{{ route('report-user.create', ['user' => '@'.Str::slug($profile->full_name), 'name' => $profile->full_name]) }}"
                       class="inline-flex items-center gap-2 rounded-full border border-rose-300 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M7.938 4h8.124c1.54 0 2.502 1.667 1.732 3L13.732 20c-.77 1.333-2.694 1.333-3.464 0L6.206 7c-.77-1.333.192-3 1.732-3z" /></svg>
                        Laporkan Pengguna
                    </a>
                </div>
            </div>
            <p class="text-slate-600 leading-relaxed">{{ $profile->bio }}</p>
            <div class="flex flex-wrap gap-4 text-sm text-slate-500">
                @if ($profile->location)
                    <span class="inline-flex items-center gap-2">
                        <span>📍</span>{{ $profile->location }}
                    </span>
                @endif
                @if ($profile->joined_at)
                    <span>Bergabung {{ optional($profile->joined_at)->translatedFormat('F Y') }}</span>
                @endif
            </div>
        </div>

        {{-- Statistik --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Statistik Donasi</p>
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

        {{-- Review dihapus sesuai permintaan. Tidak ada konten ulasan. --}}

        {{-- Kontak --}}
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">Hubungi</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-100 p-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Email</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $profile->contact_email ?? 'Tidak dicantumkan' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Telepon</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $profile->contact_phone ?? 'Tidak dicantumkan' }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 text-xs">
                @foreach ($profile->social_links ?? [] as $platform => $url)
                    <a href="{{ $url }}" target="_blank"
                        class="rounded-full bg-slate-100 px-4 py-2 font-semibold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600">
                        {{ ucfirst($platform) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
