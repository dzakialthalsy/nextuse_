<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CreateReviewController extends Controller
{
    /**
     * Menampilkan form untuk memberikan review.
     */
    public function create(Request $request)
    {
        // Check if organization is authenticated
        $reviewerId = $request->session()->get('organization_id');
        if (!$reviewerId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk memberikan review.');
        }

        // Get organization_id from query parameter and convert to integer
        $reviewedOrganizationId = $request->query('organization_id');
        
        if (!$reviewedOrganizationId) {
            return redirect()->route('beranda')->with('error', 'Organization ID tidak ditemukan. Pastikan URL berisi parameter organization_id.');
        }

        // Convert to integer to ensure proper comparison
        $reviewedOrganizationId = (int) $reviewedOrganizationId;
        
        if ($reviewedOrganizationId <= 0) {
            return redirect()->route('beranda')->with('error', 'Organization ID tidak valid.');
        }

        $reviewedOrganization = Organization::find($reviewedOrganizationId);
        
        if (!$reviewedOrganization) {
            return redirect()->route('beranda')->with('error', 'Organisasi dengan ID ' . $reviewedOrganizationId . ' tidak ditemukan.');
        }

        // Prevent organizations from reviewing themselves
        // Convert reviewerId to integer for proper comparison
        $reviewerId = (int) $reviewerId;
        if ($reviewerId == $reviewedOrganizationId) {
            return redirect()
                ->route('beranda')
                ->with('error', 'Anda tidak dapat memberikan review kepada organisasi sendiri. (Reviewer ID: ' . $reviewerId . ', Reviewed ID: ' . $reviewedOrganizationId . ')');
        }

        // Calculate average rating and transaction count (placeholder)
        $averageRating = Review::where('reviewed_organization_id', $reviewedOrganizationId)
            ->avg('rating') ?? 0;
        $reviewCount = Review::where('reviewed_organization_id', $reviewedOrganizationId)->count();
        
        // For now, using review count as transaction count placeholder
        $transactionCount = $reviewCount; // Replace with actual transaction count when available

        return view('create-review', [
            'reviewedOrganization' => $reviewedOrganization,
            'averageRating' => round($averageRating, 1),
            'transactionCount' => $transactionCount,
        ]);
    }

    /**
     * Menyimpan review baru.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if organization is authenticated
        $reviewerId = $request->session()->get('organization_id');
        if (!$reviewerId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk memberikan review.');
        }

        $validator = Validator::make($request->all(), [
            'reviewed_organization_id' => 'required|exists:organizations,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review_text' => 'required|string|min:20|max:500',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,jpg,png|max:2048', // 2MB max
            'show_name' => 'nullable|boolean',
        ], [
            'reviewed_organization_id.required' => 'Organization ID wajib diisi',
            'reviewed_organization_id.exists' => 'Organisasi tidak ditemukan',
            'rating.required' => 'Rating wajib dipilih',
            'rating.min' => 'Rating minimal 1 bintang',
            'rating.max' => 'Rating maksimal 5 bintang',
            'review_text.required' => 'Ulasan wajib diisi',
            'review_text.min' => 'Ulasan minimal 20 karakter',
            'review_text.max' => 'Ulasan maksimal 500 karakter',
            'images.max' => 'Maksimal 3 gambar',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus JPG atau PNG',
            'images.*.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prevent organizations from reviewing themselves
        if ($reviewerId == $request->reviewed_organization_id) {
            return back()
                ->withErrors(['reviewed_organization_id' => 'Anda tidak dapat memberikan review kepada organisasi sendiri.'])
                ->withInput();
        }

        // Check if organization already reviewed this organization (optional - remove if multiple reviews are allowed)
        $existingReview = Review::where('reviewer_id', $reviewerId)
            ->where('reviewed_organization_id', $request->reviewed_organization_id)
            ->first();

        if ($existingReview) {
            return back()
                ->withErrors(['review' => 'Anda sudah memberikan review untuk organisasi ini.'])
                ->withInput();
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $path = $image->store('reviews', 'public');
                    if ($path) {
                        $imagePaths[] = $path;
                    }
                }
            }
        }

        // Create review
        Review::create([
            'reviewed_organization_id' => $request->reviewed_organization_id,
            'reviewer_id' => $reviewerId,
            'rating' => $request->rating,
            'title' => $request->title,
            'review_text' => $request->review_text,
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'show_name' => $request->has('show_name') ? (bool)$request->show_name : true,
        ]);

        return redirect()
            ->route('review.read', ['organization_id' => $request->reviewed_organization_id])
            ->with('success', 'Review berhasil dikirim. Terima kasih atas ulasan Anda!');
    }
}

