<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Profile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostItemController extends Controller
{
    /**
     * Menampilkan form untuk posting barang baru.
     */
    public function create(Request $request)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }
        if ($request->session()->get('is_donor') !== true) {
            return redirect()->route('beranda');
        }

        $organizationId = (int) $request->session()->get('organization_id');

        $profile = null;
        if (Schema::hasTable('profiles')) {
            $profile = Profile::firstOrCreate(
                ['organization_id' => $organizationId],
                [
                    'full_name' => $request->session()->get('organization_name', 'Pengguna NextUse'),
                ]
            );
        }

        $editableItem = null;
        if ($request->filled('item')) {
            if ($organizationId <= 0) {
                return redirect()->route('login');
            }

            $editableItem = Item::where('organization_id', $organizationId)
                ->where('is_draft', false)
                ->find($request->item);

            if (! $editableItem) {
                return redirect()->route('inventory.index')
                    ->withErrors(['error' => 'Barang tidak ditemukan atau tidak dapat diedit.']);
            }
        }
        
        return view('posting-item', [
            'item' => $editableItem,
            'profile' => $profile,
        ]);
    }

    /**
     * Menyimpan barang yang diposting.
     */
    public function store(Request $request)
    {
        if ($request->session()->get('is_donor') !== true) {
            return redirect()->route('beranda');
        }
        // Pastikan organization_id ada di session
        $organizationId = $request->session()->get('organization_id');
        if (empty($organizationId) || !is_numeric($organizationId)) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Anda harus login terlebih dahulu.']);
        }
        
        // Convert ke integer untuk memastikan tipe data benar
        $organizationId = (int) $organizationId;
        
        $isEditing = $request->filled('item_id');

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:Elektronik,Perabotan,Pakaian,Buku & Alat Tulis,Mainan & Hobi,Olahraga,Dapur,Lainnya',
            'kondisi' => 'required|in:baru,like-new,bekas',
            'deskripsi' => 'required|string|min:30',
            'lokasi' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'status' => 'nullable|in:tersedia,habis',
            'preferensi' => 'nullable|array',
            'preferensi.*' => 'in:giveaway',
            'catatan_pengambilan' => 'nullable|string|max:1000',
            'applicant_requirements' => 'nullable|string|max:2000',
            'foto_barang' => [
                $isEditing ? 'nullable' : 'required',
                'array',
                'max:8',
                $isEditing ? 'min:0' : 'min:1',
            ],
            'foto_barang.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
            'setuju_kebijakan' => 'required|accepted',
            'item_id' => $isEditing ? 'required|integer|exists:items,id' : 'nullable',
            'existing_foto_barang' => 'nullable|array',
            'existing_foto_barang.*' => 'string',
        ], [
            'judul.required' => 'Judul barang wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'kondisi.required' => 'Kondisi barang wajib dipilih',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'deskripsi.min' => 'Deskripsi minimal 30 karakter',
            'lokasi.required' => 'Lokasi wajib diisi',
            'jumlah.required' => 'Jumlah barang wajib diisi',
            'jumlah.integer' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 1',
            'foto_barang.required' => 'Minimal 1 foto wajib diunggah',
            'foto_barang.min' => 'Minimal 1 foto wajib diunggah',
            'foto_barang.max' => 'Maksimal 8 foto',
            'foto_barang.*.image' => 'File harus berupa gambar',
            'foto_barang.*.max' => 'Ukuran file maksimal 5MB',
            'setuju_kebijakan.required' => 'Anda harus menyetujui syarat dan ketentuan',
            'setuju_kebijakan.accepted' => 'Anda harus menyetujui syarat dan ketentuan',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Upload foto
        $fotoPaths = $request->input('existing_foto_barang', []);
        if ($request->hasFile('foto_barang')) {
            foreach ($request->file('foto_barang') as $foto) {
                if ($foto->isValid()) {
                    $path = $foto->store('items', 'public');
                    $fotoPaths[] = $path;
                }
            }
        }

        if (! $isEditing && empty($fotoPaths)) {
            return back()
                ->withErrors(['foto_barang' => 'Minimal 1 foto wajib diunggah dan valid'])
                ->withInput();
        }

        $preferensi = ['giveaway'];

        $organizationId = (int) $request->session()->get('organization_id');
        if ($organizationId <= 0) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Session tidak valid. Silakan login kembali.']);
        }

        $itemData = [
            'organization_id' => $organizationId,
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'kondisi' => $request->kondisi,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'status' => $request->status ?? 'tersedia',
            'jumlah' => (int) $request->jumlah,
            'preferensi' => $preferensi,
            'catatan_pengambilan' => $request->catatan_pengambilan,
            'applicant_requirements' => $request->applicant_requirements,
            'foto_barang' => $fotoPaths,
            'is_draft' => false,
        ];

        if ($isEditing) {
            $item = Item::where('organization_id', $organizationId)->findOrFail((int) $request->item_id);
            $item->update($itemData);

            return redirect()->route('inventory.index')
                ->with('success', 'Barang berhasil diperbarui');
        }

        $item = new Item();
        $item->organization_id = $organizationId;
        $item->judul = $itemData['judul'];
        $item->kategori = $itemData['kategori'];
        $item->kondisi = $itemData['kondisi'];
        $item->deskripsi = $itemData['deskripsi'];
        $item->lokasi = $itemData['lokasi'];
        $item->status = $itemData['status'];
        $item->jumlah = $itemData['jumlah'];
        $item->preferensi = $itemData['preferensi'];
        $item->catatan_pengambilan = $itemData['catatan_pengambilan'];
        $item->applicant_requirements = $itemData['applicant_requirements'];
        $item->foto_barang = $itemData['foto_barang'];
        $item->is_draft = false;
        $item->save();

        return redirect()->route('inventory.index')
            ->with('success', 'Postingan berhasil diterbitkan');
    }

}
