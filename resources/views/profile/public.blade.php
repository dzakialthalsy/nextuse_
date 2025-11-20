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

        {{-- Review --}}
        <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-sm font-semibold text-slate-900">Review Komunitas</h2>
                <p class="text-xs text-slate-500">Umpan balik pengguna mengenai {{ $profile->full_name }}.</p>
            </div>
            <div class="px-6 py-5 space-y-6">
                <div class="grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Rata-rata</p>
                        <p class="mt-2 text-5xl font-bold text-slate-900">
                            {{ number_format($reviewStats['average'] ?? 0, 1) }}
                        </p>
                        <div class="mt-3 flex justify-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="h-5 w-5 {{ $i <= floor($reviewStats['average'] ?? 0) ? 'text-yellow-400' : 'text-slate-200' }}"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $reviewStats['total'] ?? 0 }} ulasan</p>
                    </div>
                    <div class="space-y-3">
                        @for ($i = 5; $i >= 1; $i--)
                            @php
                                $count = $reviewStats['distribution'][$i] ?? 0;
                                $percentage = ($reviewStats['total'] ?? 0) > 0 ? ($count / $reviewStats['total']) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="w-10">{{ $i }} ⭐</span>
                                <div class="flex-1 rounded-full bg-slate-100 h-2.5">
                                    <div class="h-2.5 rounded-full bg-emerald-500 transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="w-8 text-right text-xs font-semibold text-slate-500">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-900">Ulasan Terbaru</h3>
                        <a href="{{ route('review.read', ['organization_id' => $organization->id]) }}"
                           class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat semua ulasan →</a>
                    </div>
                    <div class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse ($reviewStats['latest'] ?? [] as $review)
                            <div class="py-4">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-slate-900">
                                        {{ ($review->show_name && $review->reviewer) ? $review->reviewer->organization_name : 'Pengguna NextUse' }}
                                    </p>
                                    <span class="text-xs text-slate-400">{{ $review->created_at?->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-1 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-slate-600 mt-1">{{ $review->review_text ? Str::limit($review->review_text, 140) : 'Tidak ada komentar.' }}</p>
                            </div>
                        @empty
                            <div class="py-4 text-sm text-slate-500">Belum ada ulasan dari komunitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

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

