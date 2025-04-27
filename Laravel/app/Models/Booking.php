<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['guest_id', 'room', 'check_in', 'check_out'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

