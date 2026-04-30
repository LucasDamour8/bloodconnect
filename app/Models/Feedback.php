<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // Change this to match your actual database table name
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'status',
        'admin_reply'
    ];

    /**
     * Relationship: A feedback message belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}