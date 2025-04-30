<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
use HasFactory, Notifiable;

protected $fillable = ['name', 'email', 'password', 'role'];

public function getJWTIdentifier()
{
return $this->getKey();
}

public function getJWTCustomClaims(): array
{
return [];
}
}
