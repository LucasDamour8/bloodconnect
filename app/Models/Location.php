<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    /**
     * The attributes that are mass assignable.
     */
protected $fillable = [
    'name',
    'address',
    'city',
    'phone',
    'hours',
    'availability',
    'walk_ins',
    'latitude',
    'longitude',
    'is_active',
    // ADD THESE MISSING FIELDS:
    'active_from',
    'active_until',
    'max_donors',
];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * The doctors assigned to this location.
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'location_user', 'location_id', 'user_id')
                    ->withTimestamps();
    }
}