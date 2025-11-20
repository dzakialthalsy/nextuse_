<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;

class ShowController extends Controller
{
    /**
     * Menampilkan detail barang.
     */
    public function __invoke($id)
    {
        $item = Item::with('organization')
            ->where('is_draft', false)
            ->findOrFail($id);

        return view('detail-barang', [
            'item' => $item,
        ]);
    }
}


