@extends('layouts.app')

@section('title', 'Chat - ' . $conversation['name'] . ' - NextUse')

@section('content')
    <div class="max-w-4xl mx-auto flex flex-col h-[calc(100vh-120px)]">
        {{-- Chat Header --}}
        <div class="flex items-center gap-4 border-b border-slate-200 bg-white px-4 py-4">
            <a href="{{ route('chat.index', ['from_conversation' => $conversation['id']]) }}" class="flex-shrink-0 text-slate-600 hover:text-slate-900">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-semibold flex-shrink-0">
                    {{ $conversation['avatar'] }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-900 truncate">{{ $conversation['name'] }}</p>
                    <div class="flex items-center gap-1.5">
                        <svg class="h-3 w-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                        </svg>
                        <p class="text-xs text-emerald-600 font-medium truncate">{{ $conversation['item'] }}</p>
                    </div>
                </div>
            </div>

            <button type="button" class="flex-shrink-0 text-slate-600 hover:text-slate-900">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </button>
        </div>

        {{-- Chat Messages --}}
        <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-6 space-y-4">
            @foreach ($messages as $message)
                @php
                    $isCurrentUser = $currentOrganizationId === $message->seller_id ? $message->is_owner : ! $message->is_owner;
                    $timeLabel = optional($message->sent_at ?? $message->created_at)->translatedFormat('H:i');
                @endphp
                <div class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }}">
                    <div class="flex max-w-[75%] flex-col gap-1 {{ $isCurrentUser ? 'items-end' : 'items-start' }}">
                        <div class="rounded-2xl px-4 py-2.5 text-sm leading-relaxed space-y-2 {{ $isCurrentUser ? 'bg-gradient-to-br from-emerald-500 to-teal-500 text-white' : 'bg-white text-slate-700 shadow-sm' }}">
                            @if ($message->body)
                                <p>{{ $message->body }}</p>
                            @endif
                            @if ($message->attachment_path)
                                <img src="{{ asset('storage/' . $message->attachment_path) }}" alt="Lampiran" class="rounded-xl max-h-60 object-cover border {{ $isCurrentUser ? 'border-white/30' : 'border-slate-100' }}">
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-[10px] text-slate-400">
                            <span>{{ $timeLabel }}</span>
                            @if ($isCurrentUser)
                                <span>• {{ $message->is_read ? 'Dibaca' : 'Terkirim' }}</span>
                                <form action="{{ route('chat.destroy', $message) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-[10px] px-2 py-1 rounded-full border border-slate-200 hover:bg-slate-50 {{ $isCurrentUser ? 'text-white/80 border-white/40 hover:bg-white/10' : '' }}">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7v10m6-10v10M4 7h16l-1 13a2 2 0 01-2 2H7a2 2 0 01-2-2L4 7z" /></svg>
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input Bar --}}
        <div class="border-t border-slate-200 bg-white px-4 py-3">
            <form action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation['id'] }}">

                <div class="relative">
                    <button type="button" id="attachment-trigger" class="flex-shrink-0 text-slate-400 hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <input type="file" name="attachment" id="attachment-input" accept="image/*" class="hidden">
                </div>

                <div class="flex-1">
                    <input type="text" name="body" placeholder="Ketik pesan..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-emerald-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </div>

                <button type="submit" class="flex-shrink-0 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 p-2.5 text-white hover:shadow-lg transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
            <p id="attachment-label" class="mt-2 text-xs text-slate-500 hidden"></p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const attachmentTrigger = document.getElementById('attachment-trigger');
            const attachmentInput = document.getElementById('attachment-input');
            const attachmentLabel = document.getElementById('attachment-label');
            const chatInput = document.querySelector('input[name=\"body\"]');

            attachmentTrigger?.addEventListener('click', () => {
                attachmentInput.click();
            });

            attachmentInput?.addEventListener('change', () => {
                if (attachmentInput.files.length > 0) {
                    attachmentLabel.textContent = `Lampiran: ${attachmentInput.files[0].name}`;
                    attachmentLabel.classList.remove('hidden');
                } else {
                    attachmentLabel.classList.add('hidden');
                }
            });
        });
    </script>
@endpush

