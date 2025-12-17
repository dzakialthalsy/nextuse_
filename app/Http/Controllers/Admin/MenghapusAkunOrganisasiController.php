<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class MenghapusAkunOrganisasiController extends Controller
{
    /**
     * Hapus data organisasi.
     * Use Case: Menghapus akun organisasi (Admin)
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return back()->with('success', 'Data pengguna berhasil dihapus.');
    }
}
