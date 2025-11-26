<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }
        if ($request->session()->get('is_donor') !== true) {
            return redirect()->route('beranda');
        }
        
        $organizationId = $request->session()->get('organization_id');
        $query = Item::where('organization_id', $organizationId)
            ->where('is_draft', false);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->has('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $sortBy = $request->get('sort', 'tanggal-desc');
        switch ($sortBy) {
            case 'tanggal-asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'judul-asc':
                $query->orderBy('judul', 'asc');
                break;
            case 'judul-desc':
                $query->orderBy('judul', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $items = $query->paginate(10);
        return view('inventory', compact('items'));
    }
    public function edit(Request $request, $id)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }
        
        $organizationId = $request->session()->get('organization_id');
        $item = Item::where('organization_id', $organizationId)->findOrFail($id);
        return view('edit', compact('item'));
    }

    public function update(Request $request, $id)
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
            'jumlah' => 'required|integer|min:1',
            'status' => 'nullable|in:tersedia,reserved,habis',
            'preferensi' => 'nullable|array',
            'preferensi.*' => 'in:giveaway',
            'catatan_pengambilan' => 'nullable|string|max:1000',
            'applicant_requirements' => 'nullable|string|max:2000',
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
            'jumlah' => (int) $request->jumlah,
            'preferensi' => ['giveaway'],
            'catatan_pengambilan' => $request->catatan_pengambilan,
            'applicant_requirements' => $request->applicant_requirements,
            'foto_barang' => $fotoPaths,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
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

    public function updateStatus(Request $request)
    {
        if (!$request->session()->has('organization_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
            'status' => 'required|in:tersedia,reserved,habis',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $organizationId = $request->session()->get('organization_id');

        $items = Item::where('organization_id', $organizationId)
            ->whereIn('id', $request->item_ids)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diubah', 'count' => $items]);
    }

    public function bulkDestroy(Request $request)
    {
        if (!$request->session()->has('organization_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $organizationId = $request->session()->get('organization_id');
        $items = Item::where('organization_id', $organizationId)
            ->whereIn('id', $request->item_ids)
            ->get();

        foreach ($items as $item) {
            if ($item->foto_barang) {
                foreach ($item->foto_barang as $foto) {
                    if (Storage::disk('public')->exists($foto)) {
                        Storage::disk('public')->delete($foto);
                    }
                }
            }
        }

        Item::where('organization_id', $organizationId)
            ->whereIn('id', $request->item_ids)
            ->delete();

        return response()->json(['success' => true, 'message' => count($items) . ' barang berhasil dihapus', 'count' => count($items)]);
    }

    /**
     * Menampilkan detail barang.
     */
    public function show($id)
    {
        $item = Item::with(['organization.profile'])
            ->where('is_draft', false)
            ->findOrFail($id);

        return view('detail-barang', [
            'item' => $item,
        ]);
    }
}
