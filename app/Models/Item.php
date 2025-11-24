<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'judul',
        'kategori',
        'kondisi',
        'deskripsi',
        'lokasi',
        'status',
        'jumlah',
        'preferensi',
        'catatan_pengambilan',
        'foto_barang',
        'is_draft',
    ];

    protected $casts = [
        'preferensi' => 'array',
        'foto_barang' => 'array',
        'is_draft' => 'boolean',
    ];

    /**
     * Get the organization that owns the item.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
