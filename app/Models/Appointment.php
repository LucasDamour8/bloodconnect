<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // <--- ADD THIS LINE

class Appointment extends Model
{
    protected $fillable = [
        'user_id', 
        'location_id', 
        'doctor_id', 
        'completed_by', 
        'tracking_id', 
        'appointment_date', 
        'appointment_time', 
        'donation_type', 
        'status'
    ];

    /**
     * Relationship to the actual Donation record
     * This is what allows you to fetch the saved doctor names
     */
    public function donation(): HasOne
    {
        return $this->hasOne(Donation::class, 'appointment_id');
    }

    /**
     * Relationship to the Doctor (assigned medical staff)
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Relationship to the staff member who completed the donation
     */
    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Relationship to the Donor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}