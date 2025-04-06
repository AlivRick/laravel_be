<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class ShowtimeSeat extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'showtime_seat';
    protected $primaryKey = 'showtime_seat_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'showtime_seat_id',
        'showtime_id',
        'seat_id',
        'is_booked',
    ];

    // Relationship
    public function showtime()
    {
        return $this->belongsTo(Showtime::class, 'showtime_id', 'showtime_id');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seat_id', 'seat_id');
    }
}
