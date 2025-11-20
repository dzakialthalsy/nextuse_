<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewed_organization_id',
        'reviewer_id',
        'rating',
        'title',
        'review_text',
        'images',
        'show_name',
        'transaction_id',
    ];

    protected $casts = [
        'images' => 'array',
        'show_name' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * Get the organization being reviewed.
     */
    public function reviewedOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'reviewed_organization_id');
    }

    /**
     * Get the organization who wrote the review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'reviewer_id');
    }
}

