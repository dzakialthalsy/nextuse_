<?php

namespace App\Notifications;

use App\Models\ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ItemRequestStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        protected ItemRequest $itemRequest,
        protected string $customMessage = ''
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $item = $this->itemRequest->item;

        return [
            'item_request_id' => $this->itemRequest->id,
            'item_id' => $item?->id,
            'item_title' => $item?->judul,
            'status' => $this->itemRequest->status,
            'message' => $this->customMessage ?: $this->buildDefaultMessage(),
            'requested_quantity' => $this->itemRequest->requested_quantity,
            'review_notes' => $this->itemRequest->review_notes,
        ];
    }

    protected function buildDefaultMessage(): string
    {
        return match ($this->itemRequest->status) {
            'approved' => 'Permohonan Anda disetujui oleh donatur.',
            'rejected' => 'Permohonan Anda tidak dapat dipenuhi.',
            default => 'Status permohonan Anda diperbarui.',
        };
    }
}

