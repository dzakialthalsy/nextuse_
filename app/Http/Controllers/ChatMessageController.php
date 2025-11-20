<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChatMessageController extends Controller
{
    /**
     * Menampilkan daftar percakapan.
     */
    public function index(Request $request): View
    {
        $organizationId = $request->session()->get('organization_id');

        if (!$organizationId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $latestMessageIds = ChatMessage::query()
            ->where(function ($query) use ($organizationId) {
                $query->where('buyer_id', $organizationId)
                    ->orWhere('seller_id', $organizationId);
            })
            ->selectRaw('MAX(id) as id')
            ->groupBy('conversation_id')
            ->pluck('id');

        $conversationQuery = ChatMessage::query();
        if ($latestMessageIds->isEmpty()) {
            $latestMessages = collect();
        } else {
            $latestMessages = $conversationQuery
                ->whereIn('id', $latestMessageIds)
                ->orderByDesc('sent_at')
                ->get();
        }

        $conversations = $latestMessages
            ->sortByDesc(fn (ChatMessage $message) => $message->sent_at ?? $message->created_at)
            ->map(function (ChatMessage $message) use ($organizationId) {
                $contactName = $organizationId === $message->seller_id
                    ? $message->buyer_name
                    : $message->seller_name;

                $lastMessagePreview = $message->body ?: ($message->attachment_path ? '📎 Foto dikirim' : '');
                $timestamp = optional($message->sent_at ?? $message->created_at)
                    ?->timezone(config('app.timezone'))
                    ?->diffForHumans();

                return [
                    'id' => $message->conversation_id,
                    'name' => $contactName ?? 'Pengguna',
                    'avatar' => strtoupper(substr($contactName ?? 'U', 0, 1)),
                    'item' => $message->item_title ?? '-',
                    'last_message' => $lastMessagePreview,
                    'timestamp' => $timestamp,
                    'unread' => $message->is_read ? 0 : 1,
                ];
            })
            ->values();

        // Handle review prompt when user leaves a conversation
        $reviewData = null;
        $fromConversationId = $request->query('from_conversation');
        
        if ($fromConversationId) {
            $conversationMessage = ChatMessage::where('conversation_id', $fromConversationId)
                ->where(function ($query) use ($organizationId) {
                    $query->where('buyer_id', $organizationId)
                        ->orWhere('seller_id', $organizationId);
                })
                ->first();
            
            if ($conversationMessage) {
                // Get the other party's organization ID
                $reviewedOrganizationId = $organizationId === $conversationMessage->seller_id
                    ? $conversationMessage->buyer_id
                    : $conversationMessage->seller_id;
                
                // Check if user has already reviewed this organization
                $hasReviewed = Review::where('reviewer_id', $organizationId)
                    ->where('reviewed_organization_id', $reviewedOrganizationId)
                    ->exists();
                
                if (!$hasReviewed && $reviewedOrganizationId) {
                    $reviewData = [
                        'organization_id' => $reviewedOrganizationId,
                        'organization_name' => $organizationId === $conversationMessage->seller_id
                            ? $conversationMessage->buyer_name
                            : $conversationMessage->seller_name,
                    ];
                }
            }
        }

        return view('chat.index', [
            'conversations' => $conversations,
            'reviewData' => $reviewData,
        ]);
    }

    /**
     * Menampilkan detail percakapan dengan pesan-pesan.
     */
    public function show(Request $request, string $conversationId): View
    {
        $organizationId = $request->session()->get('organization_id');

        if (!$organizationId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->orderBy('sent_at')
            ->get();

        if ($messages->isEmpty()) {
            abort(404);
        }

        $meta = $messages->first();

        if ($organizationId !== $meta->buyer_id && $organizationId !== $meta->seller_id) {
            abort(403);
        }

        // Tandai pesan sebagai sudah dibaca
        ChatMessage::where('conversation_id', $conversationId)
            ->where(function ($query) use ($organizationId) {
                $query->where('seller_id', $organizationId)
                    ->orWhere('buyer_id', $organizationId);
            })
            ->update(['is_read' => true]);

        $contactName = $organizationId === $meta->seller_id
            ? $meta->buyer_name
            : $meta->seller_name;

        $conversation = [
            'id' => $conversationId,
            'name' => $contactName ?? 'Pengguna',
            'avatar' => strtoupper(substr($contactName ?? 'U', 0, 1)),
            'item' => $meta->item_title ?? '-',
        ];

        return view('chat.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'currentOrganizationId' => $organizationId,
            'meta' => $meta,
        ]);
    }

    /**
     * Menyimpan pesan baru di dalam percakapan.
     */
    public function store(Request $request): RedirectResponse
    {
        $organizationId = $request->session()->get('organization_id');

        if (! $organizationId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $validated = $request->validate([
            'conversation_id' => ['required', 'string'],
            'body' => ['nullable', 'string', 'max:1000', 'required_without:attachment'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);

        $conversationMeta = ChatMessage::where('conversation_id', $validated['conversation_id'])->first();

        if (! $conversationMeta) {
            return back()->with('error', 'Percakapan tidak ditemukan.');
        }

        if ($organizationId !== $conversationMeta->seller_id && $organizationId !== $conversationMeta->buyer_id) {
            abort(403);
        }

        $senderName = $request->session()->get('organization_name', 'Pengguna NextUse');
        $isSeller = $organizationId === $conversationMeta->seller_id;

        $data = [
            'conversation_id' => $conversationMeta->conversation_id,
            'item_id' => $conversationMeta->item_id,
            'seller_id' => $conversationMeta->seller_id,
            'buyer_id' => $conversationMeta->buyer_id,
            'seller_name' => $conversationMeta->seller_name,
            'buyer_name' => $conversationMeta->buyer_name,
            'item_title' => $conversationMeta->item_title,
            'sender_name' => $senderName,
            'sender_role' => $isSeller ? 'Pemilik' : 'Pembeli',
            'body' => $validated['body'] ?? '',
            'is_owner' => $isSeller,
            'is_read' => false,
            'sent_at' => now(config('app.timezone')),
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('chat-attachments', 'public');
            $data['attachment_path'] = $path;
        }

        ChatMessage::create($data);

        return redirect()
            ->route('chat.show', $conversationMeta->conversation_id)
            ->with('status', 'Pesan berhasil dikirim.');
    }
    /**
     * Notifikasi ringkas pesan terbaru untuk dropdown.
     */
    public function notifications(Request $request)
    {
        $organizationId = $request->session()->get('organization_id');

        if (! $organizationId) {
            return response()->json([]);
        }

        $messages = ChatMessage::query()
            ->where(function ($query) use ($organizationId) {
                $query->where('buyer_id', $organizationId)
                    ->orWhere('seller_id', $organizationId);
            })
            ->orderByDesc('sent_at')
            ->limit(5)
            ->get();

        $items = $messages->map(function (ChatMessage $m) use ($organizationId) {
            $contactName = $organizationId === $m->seller_id ? $m->buyer_name : $m->seller_name;
            return [
                'conversation_id' => $m->conversation_id,
                'name' => $contactName ?? 'Pengguna',
                'preview' => $m->body ?: ($m->attachment_path ? '📎 Foto dikirim' : ''),
                'time' => optional($m->sent_at ?? $m->created_at)?->diffForHumans(),
                'unread' => ! $m->is_read,
            ];
        });

        return response()->json($items);
    }
}
