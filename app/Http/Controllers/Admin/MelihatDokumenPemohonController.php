<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MelihatDokumenPemohonController extends Controller
{
    /**
     * Download dokumen surat pengesahan lembaga pemohon.
     * Use Case: Melihat dokumen pemohon (Admin)
     */
    public function show(Request $request, $id)
    {
        // Pastikan user adalah admin (biasanya dicek via middleware, tapi kita bisa cek manual juga jika perlu)
        // Di sini kita asumsikan route sudah dilindungi middleware atau kita cek session jika ada flag is_admin
        // Namun, berdasarkan controller lain, kita cek session organization_id atau role
        
        // Note: Implementasi login admin mungkin berbeda, tapi kita ikuti pola yang ada.
        // Jika belum ada middleware admin yang ketat, kita bisa tambahkan pengecekan sederhana atau biarkan middleware menangani.
        
        $organization = Organization::findOrFail($id);

        if (!$organization->document_path || !Storage::disk('public')->exists($organization->document_path)) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        return Storage::disk('public')->download($organization->document_path);
    }
}
