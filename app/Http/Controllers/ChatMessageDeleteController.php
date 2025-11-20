<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\RedirectResponse;

class ChatMessageDeleteController extends Controller
{
    /**
     * Menghapus pesan chat.
     */
    public function destroy(ChatMessage $chatMessage): RedirectResponse
    {
        $conversationId = $chatMessage->conversation_id;

        // Hanya pengirim yang boleh menghapus pesannya
        $organizationId = session('organization_id');
        if (! $organizationId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $isSenderSeller = (int) $organizationId === (int) $chatMessage->seller_id;
        $isSenderBuyer = (int) $organizationId === (int) $chatMessage->buyer_id;
        $isMessageOwnedBySeller = (bool) $chatMessage->is_owner;

        $isSender = ($isSenderSeller && $isMessageOwnedBySeller) || ($isSenderBuyer && ! $isMessageOwnedBySeller);
        if (! $isSender) {
            return redirect()->route('chat.show', $conversationId)->with('error', 'Anda hanya dapat menghapus pesan milik Anda.');
        }

        // Hapus file lampiran jika ada
        if ($chatMessage->attachment_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($chatMessage->attachment_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($chatMessage->attachment_path);
        }

        $chatMessage->delete();

        return redirect()
            ->route('chat.show', $conversationId)
            ->with('status', 'Pesan berhasil dihapus.');
    }
}

