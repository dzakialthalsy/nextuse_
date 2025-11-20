<?php

namespace App\Http\Controllers\Admin\MengelolaDataBarang;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class DestroyUserController extends Controller
{
    /**
     * Hapus data organisasi.
     */
    public function __invoke(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return back()->with('success', 'Data pengguna berhasil dihapus.');
    }
}


