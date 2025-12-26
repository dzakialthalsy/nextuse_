<?php

namespace App\Http\Controllers\Donatur;

use Illuminate\Routing\Controller;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MempostingBarangController extends Controller
{
    /**
     * Menampilkan form untuk posting barang baru.
     * Use Case: Memposting barang (Donatur)
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

        return view('posting-item', [
            'item' => null,
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
                'required',
                'array',
                'max:8',
                'min:1',
            ],
            'foto_barang.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
            'setuju_kebijakan' => 'required|accepted',
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
        $fotoPaths = [];
        if ($request->hasFile('foto_barang')) {
            foreach ($request->file('foto_barang') as $foto) {
                if ($foto->isValid()) {
                    $path = $foto->store('items', 'public');
                    $fotoPaths[] = $path;
                }
            }
        }

        if (empty($fotoPaths)) {
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

        $item = new Item();
        $item->organization_id = $organizationId;
        $item->judul = $request->judul;
        $item->kategori = $request->kategori;
        $item->kondisi = $request->kondisi;
        $item->deskripsi = $request->deskripsi;
        $item->lokasi = $request->lokasi;
        $item->status = $request->status ?? 'tersedia';
        $item->jumlah = (int) $request->jumlah;
        $item->preferensi = $preferensi;
        $item->catatan_pengambilan = $request->catatan_pengambilan;
        $item->applicant_requirements = $request->applicant_requirements;
        $item->foto_barang = $fotoPaths;
        $item->is_draft = false;
        $item->save();

        return redirect()->route('inventory.index')
            ->with('success', 'Postingan berhasil diterbitkan');
    }
}
