<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;

class Organization extends Model
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_name',
        'organization_type',
        'organization_id',
        'email',
        'phone',
        'contact_person',
        'password',
        'document_path',
        'email_verified_at',
        'is_active',
        'is_admin',
        'is_donor',
        'is_receiver',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
        'is_donor' => 'boolean',
        'is_receiver' => 'boolean',
    ];

    /**
     * Automatically hash the password whenever it is set.
     */
    public function setPasswordAttribute(?string $value): void
    {
        if (empty($value)) {
            return;
        }

        // Check if the value is already a bcrypt hash (starts with $2y$ and is 60 chars)
        $isAlreadyHashed = strlen($value) === 60 && str_starts_with($value, '$2y$');

        $this->attributes['password'] = $isAlreadyHashed
            ? $value
            : Hash::make($value);
    }

    

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Requests submitted by the organization.
     */
    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class);
    }
}
