<?php

namespace App\Http\Controllers\Donatur;

use Illuminate\Routing\Controller;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Notifications\ItemRequestStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenentukanPenerimaController extends Controller
{
    /**
     * Keputusan menyetujui atau menolak permohonan.
     * Use Case: Menentukan penerima (Donatur)
     */
    public function keputusan(Request $request, ItemRequest $itemRequest): RedirectResponse
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

    /**
     * Tetapkan pemohon yang menerima barang donatur (Assign Winner).
     * Use Case: Menentukan penerima (Donatur)
     */
    public function tetapkan(Request $request, ItemRequest $itemRequest): RedirectResponse
    {
        if (! $request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        if ($request->session()->get('is_donor') !== true) {
            return redirect()->route('beranda');
        }

        $organizationId = (int) $request->session()->get('organization_id');

        $itemRequest->loadMissing('item');

        if ($itemRequest->item?->organization_id !== $organizationId) {
            abort(403, 'Anda tidak memiliki akses ke permohonan ini.');
        }

        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($itemRequest, $validated) {
            $item = Item::query()
                ->where('id', $itemRequest->item_id)
                ->lockForUpdate()
                ->first();

            if (! $item) {
                throw ValidationException::withMessages([
                    'permohonan' => 'Barang tidak ditemukan atau sudah dihapus.',
                ]);
            }

            if ($item->jumlah < $itemRequest->requested_quantity) {
                throw ValidationException::withMessages([
                    'permohonan' => 'Jumlah tersedia hanya '.$item->jumlah.' unit.',
                ]);
            }

            $item->jumlah -= $itemRequest->requested_quantity;
            if ($item->jumlah <= 0) {
                $item->jumlah = 0;
                $item->status = 'habis';
            }
            $item->save();

            if ($item->jumlah === 0) {
                ItemRequest::where('item_id', $itemRequest->item_id)
                    ->where('id', '!=', $itemRequest->id)
                    ->whereIn('status', ['pending', 'review'])
                    ->update([
                        'status' => 'rejected',
                        'review_notes' => 'Maaf, barang ini sudah diberikan kepada pemohon lain.',
                        'reviewed_at' => now(),
                    ]);
            }

            $itemRequest->update([
                'status' => 'approved',
                'review_notes' => $validated['review_notes'] ?? 'Permohonan Anda disetujui oleh donatur.',
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', 'Pemohon berhasil ditetapkan sebagai penerima barang.');
    }

    protected function approveRequest(ItemRequest $itemRequest, ?string $notes = null): void
    {
        $itemRequest->loadMissing('item', 'organization');

        $item = Item::query()
            ->where('id', $itemRequest->item_id)
            ->lockForUpdate()
            ->first();

        if (! $item) {
            throw ValidationException::withMessages([
                'permohonan' => 'Barang tidak ditemukan atau sudah dihapus.',
            ]);
        }

        if ($item->jumlah < $itemRequest->requested_quantity) {
            throw ValidationException::withMessages([
                'permohonan' => 'Jumlah tersedia hanya '.$item->jumlah.' unit. Perbarui stok sebelum menyetujui permohonan ini.',
            ]);
        }

        $item->jumlah -= $itemRequest->requested_quantity;
        if ($item->jumlah <= 0) {
            $item->jumlah = 0;
            $item->status = 'habis';
        }
        $item->save();

        $itemRequest->update([
            'status' => 'approved',
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $itemRequest->organization?->notify(new ItemRequestStatusUpdated($itemRequest, 'Selamat! Permohonan Anda disetujui oleh donatur.'));

        if ($item->jumlah === 0) {
            $this->rejectRemainingRequests($itemRequest);
        }
    }

    protected function rejectRequest(ItemRequest $itemRequest, ?string $notes = null): void
    {
        $itemRequest->update([
            'status' => 'rejected',
            'review_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $itemRequest->organization?->notify(new ItemRequestStatusUpdated($itemRequest, 'Mohon maaf, permohonan Anda belum dapat disetujui.'));
    }

    protected function rejectRemainingRequests(ItemRequest $approvedRequest): void
    {
        ItemRequest::where('item_id', $approvedRequest->item_id)
            ->where('id', '!=', $approvedRequest->id)
            ->whereIn('status', ['pending', 'review'])
            ->chunk(100, function ($requests) {
                foreach ($requests as $request) {
                    $request->update([
                        'status' => 'rejected',
                        'review_notes' => 'Barang telah habis diberikan kepada pemohon lain.',
                        'reviewed_at' => now(),
                    ]);
                    $request->organization?->notify(new ItemRequestStatusUpdated($request, 'Mohon maaf, barang telah habis.'));
                }
            });
    }
}
