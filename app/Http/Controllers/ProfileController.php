<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfGuest($request)) {
            return $redirect;
        }

        $profile = $this->profileFor($request);
        $organizationId = (int) $request->session()->get('organization_id');

        // Hitung statistik dan aktivitas untuk pemilik akun
        $stats = $this->calculateStats($organizationId);
        $activities = $this->recentActivities($organizationId);

        return view('profile.show', [
            'profile' => $profile,
            'organizationName' => $request->session()->get('organization_name'),
            'stats' => $stats,
            'activities' => $activities,
        ]);
    }

    public function edit(Request $request, Profile $profile): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfGuest($request)) {
            return $redirect;
        }

        $this->authorizeProfile($request, $profile);

        return view('profile.edit', [
            'profile' => $profile,
        ]);
    }

    public function update(Request $request, Profile $profile): RedirectResponse
    {
        if ($redirect = $this->redirectIfGuest($request)) {
            return $redirect;
        }

        $this->authorizeProfile($request, $profile);

        $data = $this->validatedData($request, $profile);

        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            $avatarName = time() . '_' . $avatarFile->getClientOriginalName();
            $avatarPath = public_path('images/profile');
            
            // Buat folder jika belum ada
            if (!file_exists($avatarPath)) {
                mkdir($avatarPath, 0755, true);
            }
            
            $avatarFile->move($avatarPath, $avatarName);
            $data['avatar_url'] = '/images/profile/' . $avatarName;
        }

        $profile->update($data);

        $organization = Organization::find($profile->organization_id);
        if ($organization) {
            $organization->organization_name = $data['full_name'];
            $organization->save();
            $request->session()->put('organization_name', $data['full_name']);
        }

        return redirect()
            ->route('profile.index')
            ->with('status', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request, Profile $profile): RedirectResponse
    {
        if ($redirect = $this->redirectIfGuest($request)) {
            return $redirect;
        }

        $this->authorizeProfile($request, $profile);

        $profile->delete();

        return redirect()
            ->route('profile.index')
            ->with('status', 'Profil direset. Data default telah diterapkan.');
    }

    protected function redirectIfGuest(Request $request): ?RedirectResponse
    {
        if (! $request->session()->has('organization_id')) {
            return redirect()
                ->route('login')
                ->with('status', 'Silakan login untuk membuka halaman profil.');
        }

        return null;
    }

    protected function profileFor(Request $request): Profile
    {
        $organizationId = (int) $request->session()->get('organization_id');

        if (! \Illuminate\Support\Facades\Schema::hasTable('profiles')) {
            // Jika tabel belum ada (mis. sebelum migrate), kembalikan instance Profile dummy
            return new Profile($this->defaultProfileAttributes($request) + [
                'organization_id' => $organizationId,
            ]);
        }

        return Profile::firstOrCreate(
            ['organization_id' => $organizationId],
            $this->defaultProfileAttributes($request->session()->get('organization_name'))
        );
    }

    protected function authorizeProfile(Request $request, Profile $profile): void
    {
        $organizationId = (int) $request->session()->get('organization_id');

        abort_unless($profile->organization_id === $organizationId, 403);
    }

    protected function validatedData(Request $request, ?Profile $profile = null): array
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'headline' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:100'],
            'availability_status' => ['nullable', 'string', 'max:120'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'completed_deals' => ['nullable', 'integer', 'min:0'],
            'followers_count' => ['nullable', 'integer', 'min:0'],
            'following_count' => ['nullable', 'integer', 'min:0'],
            'response_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'response_time' => ['nullable', 'string', 'max:100'],
            'skills_text' => ['nullable', 'string'],
            'categories_text' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'avatar_url' => ['nullable', 'string', 'max:255'],
            'cover_url' => ['nullable', 'url'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'portfolio_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'tiktok_url' => ['nullable', 'url'],
            'joined_at' => ['nullable', 'date'],
        ]);

        return [
            'full_name' => $validated['full_name'],
            'headline' => $validated['headline'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'location' => $validated['location'] ?? null,
            'availability_status' => $validated['availability_status'] ?? $profile?->availability_status ?? 'Tersedia untuk berkolaborasi',
            'rating' => $validated['rating'] ?? $profile?->rating ?? 4.9,
            'completed_deals' => $validated['completed_deals'] ?? $profile?->completed_deals ?? 0,
            'followers_count' => $validated['followers_count'] ?? $profile?->followers_count ?? 0,
            'following_count' => $validated['following_count'] ?? $profile?->following_count ?? 0,
            'response_rate' => $validated['response_rate'] ?? $profile?->response_rate ?? 98,
            'response_time' => $validated['response_time'] ?? $profile?->response_time ?? 'Dalam 1 jam',
            'skills' => $this->explodeList($validated['skills_text'] ?? '') ?: ($profile?->skills ?? []),
            'favorite_categories' => $this->explodeList($validated['categories_text'] ?? '') ?: ($profile?->favorite_categories ?? []),
            'avatar_url' => $validated['avatar_url'] ?? $profile?->avatar_url ?? null,
            'cover_url' => $validated['cover_url'] ?? $profile?->cover_url ?? null,
            'contact_email' => $validated['contact_email'] ?? $profile?->contact_email ?? null,
            'contact_phone' => $validated['contact_phone'] ?? $profile?->contact_phone ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? $profile?->portfolio_url ?? null,
            'social_links' => array_filter([
                'instagram' => $validated['instagram_url'] ?? ($profile?->social_links['instagram'] ?? null),
                'tiktok' => $validated['tiktok_url'] ?? ($profile?->social_links['tiktok'] ?? null),
            ]) ?: ($profile?->social_links ?? []),
            'joined_at' => $validated['joined_at'] ?? ($profile?->joined_at?->format('Y-m-d') ?? null),
        ];
    }

    protected function explodeList(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    protected function defaultProfileAttributes(?string $orgName = null): array
    {
        $orgName = $orgName ?: 'NextUse Partner';

        return [
            'full_name' => $orgName,
            'headline' => 'Kurator Barang Bekas Premium',
            'bio' => 'Kami membantu komunitas menghidupkan kembali barang bekas berkualitas melalui kurasi yang selektif dan pengalaman transaksi yang hangat. Fokus pada dampak sosial dan keberlanjutan.',
            'location' => 'Jakarta, Indonesia',
            'availability_status' => 'Tersedia untuk kolaborasi koleksi vintage',
            'rating' => 4.9,
            'completed_deals' => 128,
            'followers_count' => 3200,
            'following_count' => 180,
            'response_rate' => 99,
            'response_time' => '± 30 menit',
            'skills' => ['Kurasi Produk', 'Storytelling', 'Live Shopping'],
            'favorite_categories' => ['Elektronik', 'Fashion', 'Dekorasi'],
            'avatar_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80',
            'cover_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
            'contact_email' => 'hello@nextuse.id',
            'contact_phone' => '+62 812-3456-7890',
            'portfolio_url' => 'https://nextuse.id/portfolio',
            'social_links' => [
                'instagram' => 'https://instagram.com/nextuse.id',
                'tiktok' => 'https://tiktok.com/@nextuse.id',
            ],
            'joined_at' => now()->subYears(2),
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

        // Trades: item yang preferensi mengandung barter
        $trades = Item::where('organization_id', $organizationId)
            ->where('is_draft', false)
            ->whereJsonContains('preferensi', 'barter')
            ->count();

        return [
            'items_posted' => $itemsPosted,
            'giveaway' => $giveaway,
            'trades' => $trades,
        ];
    }

    protected function recentActivities(int $organizationId)
    {
        return Item::where('organization_id', $organizationId)
            ->where('is_draft', false)
            ->latest()
            ->take(5)
            ->get(['id', 'judul', 'created_at']);
    }

    protected function reviewSummary(int $organizationId): array
    {
        $baseQuery = Review::where('reviewed_organization_id', $organizationId);
        $totalReviews = (clone $baseQuery)->count();
        $averageRating = $totalReviews ? round((clone $baseQuery)->avg('rating'), 1) : 0;
        $distribution = Review::where('reviewed_organization_id', $organizationId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
        $latestReviews = Review::with('reviewer:id,organization_name')
            ->where('reviewed_organization_id', $organizationId)
            ->latest()
            ->take(3)
            ->get();

        return [
            'total' => $totalReviews,
            'average' => $averageRating,
            'distribution' => $distribution,
            'latest' => $latestReviews,
        ];
    }

    /**
     * Tampilan profil publik untuk organisasi lain.
     */
    public function showPublic(Organization $organization): View
    {
        $profile = Profile::firstOrCreate(
            ['organization_id' => $organization->id],
            $this->defaultProfileAttributes($organization->organization_name)
        );

        $stats = $this->calculateStats($organization->id);
        $reviewStats = $this->reviewSummary($organization->id);

        return view('profile.public', [
            'organization' => $organization,
            'profile' => $profile,
            'stats' => $stats,
            'reviewStats' => $reviewStats,
        ]);
    }
}
