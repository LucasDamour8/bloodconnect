<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterCapacity extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * * @var string
     */
    protected $table = 'center_capacities';

    /**
     * The attributes that are mass assignable.
     * These must match the keys used in your LocationController loop.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'location_id',
        'date',
        'max_donors',
    ];

    /**
     * Get the location that owns the capacity record.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}