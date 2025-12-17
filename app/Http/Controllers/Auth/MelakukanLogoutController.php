<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MelakukanLogoutController extends Controller
{
    /**
     * Logout dari sesi organisasi.
     * Use Case: Melakukan logout (Semua User)
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'organization_id',
            'organization_name',
            'is_admin',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar dari akun.');
    }
}
