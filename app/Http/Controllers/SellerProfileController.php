<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SellerProfileController extends Controller
{
    /**
     * Tampilkan profil publik pengunggah barang.
     */
    public function __invoke(Request $request, Organization $organization): RedirectResponse
    {
        return redirect()->route('profile.public', $organization);
    }
}


