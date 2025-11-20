@extends('layouts.app')

@section('title', $profile->full_name . ' - Profil Pengunggah')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">
        <div class="space-y-1">
            <h1 class="text-3xl font-semibold text-slate-900">Profil Pengunggah</h1>
            <p class="text-sm text-slate-500">Lihat informasi publik dari pengunggah barang ini.</p>
        </div>

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
                            @if ($organization->organization_type)
                                <p class="text-sm text-slate-500">{{ $organization->organization_type }}</p>
                            @endif
                        </div>
                        <dl class="mt-3 space-y-2 text-sm text-slate-600">
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
            </div>
        </section>
    </div>
@endsection


