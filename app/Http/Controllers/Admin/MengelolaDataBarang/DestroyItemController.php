<?php

namespace App\Http\Controllers\Admin\MengelolaDataBarang;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;

class DestroyItemController extends Controller
{
    /**
     * Hapus data barang.
     */
    public function __invoke(Item $item): RedirectResponse
    {
        $item->delete();

        return back()->with('success', 'Data barang berhasil dihapus.');
    }
}


