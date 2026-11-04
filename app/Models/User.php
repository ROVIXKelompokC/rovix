<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'district_id',
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'officer_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }
}
