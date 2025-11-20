<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestroyController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        $organizationId = $request->session()->get('organization_id');
        $item = Item::where('organization_id', $organizationId)->findOrFail($id);

        if ($item->foto_barang) {
            foreach ($item->foto_barang as $foto) {
                if (Storage::disk('public')->exists($foto)) {
                    Storage::disk('public')->delete($foto);
                }
            }
        }

        $item->delete();

        return redirect()->route('inventory.index')->with('success', 'Barang sudah terhapus');
    }
}


