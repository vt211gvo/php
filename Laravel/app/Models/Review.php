<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['guest_id', 'comment', 'rating'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
