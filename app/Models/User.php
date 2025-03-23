<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'username',
        'password',
        'email',
        'full_name',
        'phone_number',
        'date_of_birth',
        'profile_image',
        'is_active'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }

    public function movieReviews()
    {
        return $this->hasMany(MovieReview::class, 'user_id', 'user_id');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'userpromotion', 'user_id', 'promotion_id')
                    ->withPivot('is_used', 'used_at');
    }

    public function seatChanges()
    {
        return $this->hasMany(SeatChangeHistory::class, 'changed_by', 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}