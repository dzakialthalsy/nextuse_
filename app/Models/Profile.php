<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'organization_id',
        'full_name',
        'headline',
        'bio',
        'location',
        'availability_status',
        'rating',
        'completed_deals',
        'followers_count',
        'following_count',
        'response_rate',
        'response_time',
        'skills',
        'favorite_categories',
        'avatar_url',
        'cover_url',
        'contact_email',
        'contact_phone',
        'portfolio_url',
        'social_links',
        'joined_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'favorite_categories' => 'array',
        'social_links' => 'array',
        'joined_at' => 'date',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
