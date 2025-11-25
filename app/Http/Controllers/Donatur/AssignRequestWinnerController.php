<?php

namespace App\Http\Controllers\Donatur;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignRequestWinnerController extends Controller
{
    /**
     * Tetapkan pemohon yang menerima barang donatur.
     */
    public function __invoke(Request $request, ItemRequest $itemRequest): RedirectResponse
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
}

