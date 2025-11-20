<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    /**
     * Melihat Halaman Donasi Developer
     */
    public function index()
    {
        return view('dukung-nextuse');
    }

    /**
     * Melakukan Donasi melalui QRIS
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'pesan' => ['nullable', 'string', 'max:200'],
                'is_anonim' => ['nullable', 'boolean'],
            ],
            [],
            [
                'pesan' => 'pesan',
                'is_anonim' => 'donasi anonim',
            ]
        );

        // Get organization ID from session (if logged in)
        $organizationId = $request->session()->get('organization_id');

        // Create donation record
        $donation = new Donation();
        $donation->organization_id = $organizationId;
        $donation->pesan = $validated['pesan'] ?? null;
        $donation->is_anonim = $validated['is_anonim'] ?? false;
        $donation->status = 'pending'; // Status pending karena donasi melalui QRIS perlu verifikasi manual
        $donation->save();

        return redirect()->route('dukung-nextuse')
            ->with('success', 'Terima kasih atas donasi Anda! Donasi Anda akan diverifikasi setelah pembayaran melalui QRIS dikonfirmasi.');
    }
}

