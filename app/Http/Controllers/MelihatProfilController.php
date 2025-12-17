<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MelihatProfilController extends Controller
{
    /**
     * Tampilkan profil publik pengunggah barang.
     * Use Case: Melihat profil (Donatur, Pemohon)
     */
    public function __invoke(Organization $organization): View
    {
        $profile = Profile::firstOrCreate(
            ['organization_id' => $organization->id],
            $this->defaultProfileAttributes($organization->organization_name)
        );

        $stats = $this->calculateStats($organization->id);

        return view('profile.public', [
            'organization' => $organization,
            'profile' => $profile,
            'stats' => $stats,
        ]);
    }

    protected function defaultProfileAttributes(?string $orgName = null): array
    {
        return [
            'full_name' => $orgName ?: 'Organisasi',
        ];
    }

    /**
     * Hitung statistik dari database untuk organization.
     */
    protected function calculateStats(int $organizationId): array
    {
        // Items Posted: semua item yang bukan draft
        $itemsPosted = Item::where('organization_id', $organizationId)
            ->where('is_draft', false)
            ->count();

        // Giveaway: item yang preferensi mengandung 'giveaway'
        $giveaway = Item::where('organization_id', $organizationId)
            ->where('is_draft', false)
            ->whereJsonContains('preferensi', 'giveaway')
            ->count();

        return [
            'items_posted' => $itemsPosted,
            'giveaway' => $giveaway,
        ];
    }
}
