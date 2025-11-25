<?php

namespace App\Http\Controllers\Donatur;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use App\Notifications\ItemRequestStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestDecisionController extends Controller
{
    public function __invoke(Request $request, ItemRequest $itemRequest): RedirectResponse
    {
        if (! $request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        if ($request->session()->get('is_donor') !== true) {
            return redirect()->route('beranda');
        }

        $organizationId = (int) $request->session()->get('organization_id');

        if ($itemRequest->item?->organization_id !== $organizationId) {
            abort(403);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $itemRequest) {
            if ($validated['decision'] === 'approve') {
                $this->approveRequest($itemRequest, $validated['notes'] ?? null);
            } else {
                $this->rejectRequest($itemRequest, $validated['notes'] ?? null);
            }
        });

        $message = $validated['decision'] === 'approve'
            ? 'Permohonan berhasil disetujui.'
            : 'Permohonan telah ditandai tidak diterima.';

        return redirect()
            ->route('donatur.requests.index')
            ->with('success', $message);
    }

    protected function approveRequest(ItemRequest $itemRequest, ?string $notes = null): void
    {
        $itemRequest->update([
            'status' => 'approved',
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $itemRequest->organization?->notify(new ItemRequestStatusUpdated($itemRequest, 'Selamat! Permohonan Anda disetujui oleh donatur.'));

        $otherRequests = ItemRequest::query()
            ->where('item_id', $itemRequest->item_id)
            ->where('id', '!=', $itemRequest->id)
            ->whereIn('status', ['pending', 'review'])
            ->lockForUpdate()
            ->get();

        foreach ($otherRequests as $other) {
            $other->update([
                'status' => 'rejected',
                'review_notes' => 'Barang diberikan kepada pemohon lain.',
                'reviewed_at' => now(),
            ]);

            $other->organization?->notify(new ItemRequestStatusUpdated($other, 'Maaf, barang diberikan kepada pemohon lain.'));
        }
    }

    protected function rejectRequest(ItemRequest $itemRequest, ?string $notes = null): void
    {
        $itemRequest->update([
            'status' => 'rejected',
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $itemRequest->organization?->notify(new ItemRequestStatusUpdated($itemRequest, 'Permohonan Anda tidak memenuhi kriteria donatur.'));
    }
}

