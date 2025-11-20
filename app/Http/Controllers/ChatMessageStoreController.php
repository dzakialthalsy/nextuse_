<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Item;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatMessageStoreController extends Controller
{
    /**
     * Memulai percakapan baru dengan penjual.
     */
    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'seller_id' => ['required', 'exists:organizations,id'],
        ]);

        $buyerId = $request->session()->get('organization_id');

        if (!$buyerId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ((int) $buyerId === (int) $data['seller_id']) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghubungi diri sendiri.');
        }

        $buyer = Organization::findOrFail($buyerId);
        $seller = Organization::findOrFail($data['seller_id']);
        $item = Item::findOrFail($data['item_id']);

        // Cek apakah sudah ada percakapan sebelumnya
        $existing = ChatMessage::where('item_id', $item->id)
            ->where('seller_id', $seller->id)
            ->where('buyer_id', $buyer->id)
            ->first();

        if ($existing) {
            if (empty($existing->conversation_id)) {
                $conversationId = (string) Str::uuid();
                $existing->conversation_id = $conversationId;
                $existing->save();
            } else {
                $conversationId = $existing->conversation_id;
            }
        } else {
            $conversationId = (string) Str::uuid();

        ChatMessage::create([
                'conversation_id' => $conversationId,
                'item_id' => $item->id,
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'seller_name' => $seller->organization_name,
                'buyer_name' => $buyer->organization_name,
                'item_title' => $item->judul,
                'sender_name' => $buyer->organization_name,
                'sender_role' => 'buyer',
                'body' => 'Halo, saya tertarik dengan ' . $item->judul . '.',
                'is_owner' => false,
                'is_read' => false,
            'sent_at' => now(config('app.timezone')),
            ]);
        }

        return redirect()->route('chat.show', $conversationId);
    }

    /**
     * Menyimpan pesan baru ke dalam percakapan.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        ChatMessage::create($data);

        return redirect()
            ->route('chat.show', $data['conversation_id'])
            ->with('status', 'Pesan baru berhasil dikirim.');
    }

    /**
     * Validasi input pesan.
     */
    protected function validatedData(Request $request): array
    {
        $payload = $request->validate([
            'conversation_id' => ['required', 'string'],
            'body' => ['nullable', 'string', 'max:1000', 'required_without:attachment'],
            'attachment' => ['nullable', 'image', 'max:5120'],
        ]);

        $conversation = ChatMessage::where('conversation_id', $payload['conversation_id'])->firstOrFail();
        $organizationId = $request->session()->get('organization_id');

        if (!$organizationId) {
            abort(403);
        }

        $isSeller = $organizationId === $conversation->seller_id;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat-attachments', 'public');
        }

        return [
            'conversation_id' => $conversation->conversation_id,
            'item_id' => $conversation->item_id,
            'seller_id' => $conversation->seller_id,
            'buyer_id' => $conversation->buyer_id,
            'seller_name' => $conversation->seller_name,
            'buyer_name' => $conversation->buyer_name,
            'item_title' => $conversation->item_title,
            'sender_name' => $isSeller ? $conversation->seller_name : $conversation->buyer_name,
            'sender_role' => $isSeller ? 'seller' : 'buyer',
            'body' => $payload['body'] ?? '',
            'attachment_path' => $attachmentPath,
            'is_owner' => $isSeller,
            'is_read' => false,
            'sent_at' => now(config('app.timezone')),
        ];
    }
}

