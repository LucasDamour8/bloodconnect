<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'appointment_id', 
        'user_id', 
        'location_id', 
        'doctor_id', 
        'approved_by_firstname', // Added for permanent record
        'approved_by_lastname',  // Added for permanent record
        'blood_type', 
        'age', 
        'weight', 
        'blood_pressure', 
        'pulse_rate', 
        'temperature', 
        'hemoglobin', 
        'general_health',
        'hiv_test', 
        'hep_b', 
        'hep_c', 
        'syphilis', 
        'conclusion', 
        'donation_date', 
        'status'
    ];

    /**
     * Get the donor (user).
     */
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    /**
     * Get the doctor who performed the exam via relationship.
     */
    public function doctor(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'doctor_id'); 
    }

    /**
     * Get the location where the donation happened.
     */
    public function location(): BelongsTo 
    { 
        return $this->belongsTo(Location::class); 
    }

    /**
     * Get the appointment associated with this donation.
     */
    public function appointment(): BelongsTo 
    { 
        return $this->belongsTo(Appointment::class); 
    }
}