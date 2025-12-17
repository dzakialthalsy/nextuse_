<?php

namespace App\Http\Controllers\Pemohon;

use Illuminate\Routing\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class MelihatDetailBarangController extends Controller
{
    /**
     * Menampilkan detail barang.
     * Use Case: Melihat detail barang (Pemohon)
     */
    public function show(Request $request, $id)
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

    /**
     * Menampilkan halaman Syarat dan Ketentuan.
     */
    public function syaratKetentuan()
    {
        return view('syarat-ketentuan');
    }
}
