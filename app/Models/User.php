<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str; 
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'password', 
        'phone', 
        'role', 
        'custom_id', 
        'national_id', 
        'district', 
        'sector', 
        'is_active', 
        'date_of_birth', 
        'gender', 
        'blood_type', 
        'profile_photo',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $prefix = str_pad(strtoupper(substr($user->first_name, 0, 3)), 3, 'X');
            $random = strtoupper(Str::random(3));
            $user->custom_id = $prefix . $random;
        });
    }

    // ─── VIRTUAL ATTRIBUTE FOR 'NAME' ────────────────────────────────────────

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    // ─── ACCESSORS ──────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function profilePhotoUrl(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }

        $name = urlencode($this->full_name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }

    // ─── RELATIONSHIPS ───────────────────────────────────────────────────────

    /**
     * CRITICAL: This connects the Doctor to their assigned Locations/Centers
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_user', 'user_id', 'location_id')
                    ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)
                    ->withPivot('unlocked_at')
                    ->withTimestamps();
    }

    // ─── HELPER METHODS ─────────────────────────────────────────────────────

    public function totalDonations(): int
    {
        return $this->donations()->where('status', 'completed')->count();
    }
}
