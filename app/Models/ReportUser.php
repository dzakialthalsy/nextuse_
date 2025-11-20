<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportUser extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reporter_id',
        'target_user',
        'target_user_name',
        'kategori',
        'deskripsi',
        'bukti_paths',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'decision',
        'action',
        'reject_reason',
        'action_note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bukti_paths' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the organization that reported this user.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'reporter_id');
    }

    /**
     * Get the admin who reviewed this report.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'reviewed_by');
    }

    /**
     * Get all moderation history for this report.
     */
    public function moderationHistory(): MorphMany
    {
        return $this->morphMany(ModerationHistory::class, 'reportable');
    }
}
