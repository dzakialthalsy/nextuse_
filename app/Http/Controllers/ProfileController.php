<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Organization;
use App\Models\Profile;
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

        $organization = Organization::find($profile->organization_id);

        return view('profile.edit', [
            'profile' => $profile,
            'organization' => $organization,
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
            $orgEmail = $request->input('organization_email');
            $orgType = $request->input('organization_type');
            if ($orgEmail !== null) {
                $organization->email = $orgEmail;
            }
            if ($orgType !== null) {
                $organization->organization_type = $orgType;
            }
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
            // Jika tabel belum ada, kembalikan instance Profile tanpa data dummy
            return new Profile($this->defaultProfileAttributes($request->session()->get('organization_name')) + [
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
            'organization_email' => ['nullable', 'email', 'max:255'],
            'organization_type' => ['nullable', 'in:yayasan,kampus,sekolah,pemerintah,komunitas,perusahaan-sosial,lainnya'],
        ]);

        return [
            'full_name' => $validated['full_name'],
            'headline' => $validated['headline'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'location' => $validated['location'] ?? null,
            'availability_status' => $validated['availability_status'] ?? $profile?->availability_status,
            'rating' => $validated['rating'] ?? $profile?->rating,
            'completed_deals' => $validated['completed_deals'] ?? $profile?->completed_deals,
            'followers_count' => $validated['followers_count'] ?? $profile?->followers_count,
            'following_count' => $validated['following_count'] ?? $profile?->following_count,
            'response_rate' => $validated['response_rate'] ?? $profile?->response_rate,
            'response_time' => $validated['response_time'] ?? $profile?->response_time,
            'skills' => $this->explodeList($validated['skills_text'] ?? '') ?: ($profile?->skills ?? []),
            'favorite_categories' => $this->explodeList($validated['categories_text'] ?? '') ?: ($profile?->favorite_categories ?? []),
            'avatar_url' => $validated['avatar_url'] ?? $profile?->avatar_url,
            'cover_url' => $validated['cover_url'] ?? $profile?->cover_url,
            'contact_email' => $validated['contact_email'] ?? $profile?->contact_email,
            'contact_phone' => $validated['contact_phone'] ?? $profile?->contact_phone,
            'portfolio_url' => $validated['portfolio_url'] ?? $profile?->portfolio_url,
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
        return [
            'full_name' => $orgName,
            'headline' => null,
            'bio' => null,
            'location' => null,
            'availability_status' => null,
            'rating' => null,
            'completed_deals' => null,
            'followers_count' => null,
            'following_count' => null,
            'response_rate' => null,
            'response_time' => null,
            'skills' => [],
            'favorite_categories' => [],
            'avatar_url' => null,
            'cover_url' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'portfolio_url' => null,
            'social_links' => [],
            'joined_at' => null,
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

        return view('profile.public', [
            'organization' => $organization,
            'profile' => $profile,
            'stats' => $stats,
        ]);
    }
}
