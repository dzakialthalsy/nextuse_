<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Organization;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'item_id',
        'seller_id',
        'buyer_id',
        'seller_name',
        'buyer_name',
        'item_title',
        'sender_name',
        'sender_role',
        'body',
        'attachment_path',
        'is_owner',
        'is_read',
        'sent_at',
    ];

    protected $casts = [
        'is_owner' => 'boolean',
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function seller()
    {
        return $this->belongsTo(Organization::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Organization::class, 'buyer_id');
    }
}
