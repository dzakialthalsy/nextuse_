<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * Menampilkan detail barang.
     */
    public function __invoke(Request $request, $id)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        $item = Item::with('organization')
            ->where('is_draft', false)
            ->findOrFail($id);

        return view('detail-barang', [
            'item' => $item,
        ]);
    }
}


