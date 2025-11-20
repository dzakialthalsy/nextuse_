<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModerationHistory extends Model
{
    use HasFactory;

    protected $table = 'moderation_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reportable_type',
        'reportable_id',
        'moderator_id',
        'action',
        'reason',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        //
    ];

    /**
     * Get the parent reportable model (ReportItem or ReportUser).
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the moderator (admin) who performed this action.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'moderator_id');
    }
}
