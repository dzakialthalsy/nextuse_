<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Organization;
use Illuminate\Http\Request;

class ReadReviewController extends Controller
{
    /**
     * Menampilkan semua review untuk organization tertentu.
     */
    public function show(Request $request)
    {
        $organizationId = $request->query('organization_id') ?? $request->route('organization_id');
        
        if (!$organizationId) {
            return redirect()->back()->with('error', 'Organization ID tidak ditemukan.');
        }

        $organization = Organization::find($organizationId);
        
        if (!$organization) {
            return redirect()->back()->with('error', 'Organisasi tidak ditemukan.');
        }

        // Get all reviews for this organization
        $reviews = Review::where('reviewed_organization_id', $organizationId)
            ->with(['reviewer' => function ($query) {
                $query->select('id', 'organization_name', 'email');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate statistics
        $totalReviews = Review::where('reviewed_organization_id', $organizationId)->count();
        $averageRating = Review::where('reviewed_organization_id', $organizationId)->avg('rating') ?? 0;
        $ratingDistribution = Review::where('reviewed_organization_id', $organizationId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->keyBy('rating');

        // For now, using review count as transaction count placeholder
        $transactionCount = $totalReviews; // Replace with actual transaction count when available

        // Check if current organization is viewing their own reviews
        $currentOrganizationId = $request->session()->get('organization_id');
        $isOwnProfile = $currentOrganizationId && (int)$currentOrganizationId == (int)$organizationId;

        return view('read-review', [
            'organization' => $organization,
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'averageRating' => round($averageRating, 1),
            'ratingDistribution' => $ratingDistribution,
            'transactionCount' => $transactionCount,
            'isOwnProfile' => $isOwnProfile,
        ]);
    }
}

