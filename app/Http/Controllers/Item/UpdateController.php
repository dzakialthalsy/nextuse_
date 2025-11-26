<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        $organizationId = $request->session()->get('organization_id');
        $item = Item::where('organization_id', $organizationId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:Elektronik,Perabotan,Pakaian,Buku & Alat Tulis,Mainan & Hobi,Olahraga,Dapur,Lainnya',
            'kondisi' => 'required|in:baru,like-new,bekas',
            'deskripsi' => 'required|string|min:30',
            'lokasi' => 'required|string|max:255',
            'status' => 'nullable|in:tersedia,reserved,habis',
            'preferensi' => 'nullable|array',
            'preferensi.*' => 'in:giveaway',
            'foto_barang' => 'nullable|array|max:8',
            'foto_barang.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'foto_barang_old' => 'nullable|array',
            'foto_barang_old.*' => 'string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $fotoPaths = $request->foto_barang_old ?? [];
        if ($request->hasFile('foto_barang')) {
            foreach ($request->file('foto_barang') as $foto) {
                $path = $foto->store('items', 'public');
                $fotoPaths[] = $path;
            }
        }

        $item->update([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'kondisi' => $request->kondisi,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'status' => $request->status ?? $item->status,
            'preferensi' => ['giveaway'],
            'foto_barang' => $fotoPaths,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Barang berhasil diperbarui');
    }
}

