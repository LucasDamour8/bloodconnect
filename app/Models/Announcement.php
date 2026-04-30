<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    // This allows you to save data into these columns
    protected $fillable = [
        'title',
        'content',
        'image'
    ];
}