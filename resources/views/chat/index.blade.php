@extends('layouts.app')

@section('title', 'Messages - NextUse')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-4">
        {{-- Review Prompt Banner --}}
        @if($reviewData ?? null)
            <div class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-slate-900">Beri Review untuk {{ $reviewData['organization_name'] }}</h3>
                        </div>
                        <p class="text-sm text-slate-600 mb-4">Bagikan pengalaman Anda dengan {{ $reviewData['organization_name'] }} untuk membantu komunitas NextUse.</p>
                        <a href="{{ route('review.create', ['organization_id' => $reviewData['organization_id']]) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold rounded-lg shadow-sm transition-all hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            Beri Review
                        </a>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="relative">
            <input type="text" placeholder="Cari percakapan..." 
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 pl-10 text-sm shadow-sm focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
            <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-4">
            <button type="button" class="flex items-center gap-2 rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-white">
                <span>Semua</span>
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-xs">{{ $conversations->count() }}</span>
            </button>
            <button type="button" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200">
                Belum Dibaca
            </button>
            <button type="button" class="ml-auto rounded-full p-2 text-slate-400 hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        {{-- Conversation List --}}
        <div class="space-y-1 rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            @forelse ($conversations as $conversation)
                <a href="{{ route('chat.show', $conversation['id']) }}" 
                    class="flex items-center gap-4 px-4 py-4 hover:bg-slate-50 transition">
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-semibold text-lg">
                            {{ $conversation['avatar'] }}
                        </div>
                        @if ($conversation['unread'] > 0)
                            <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-semibold text-white">
                                {{ $conversation['unread'] }}
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-slate-900">{{ $conversation['name'] }}</p>
                            <p class="text-xs text-slate-400 whitespace-nowrap">{{ $conversation['timestamp'] }}</p>
                        </div>
                        <p class="text-xs text-emerald-600 font-medium mt-0.5 truncate">{{ $conversation['item'] }}</p>
                        <p class="text-sm text-slate-600 mt-1 truncate">{{ $conversation['last_message'] }}</p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-sm text-slate-500">
                    Belum ada percakapan.
                </div>
            @endforelse
        </div>
    </div>
@endsection
