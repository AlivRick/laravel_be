<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
use Illuminate\Support\Facades\Cache;
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

    
    public static function isSeatReserved($showtimeId, $seatId)
    {
        $key = "reservation:{$showtimeId}:{$seatId}";
        return Cache::has($key);
    }

    public static function reserveSeat($showtimeId, $seatId, $ttl = 900) // 15 phút = 900 giây
    {
        $key = "reservation:{$showtimeId}:{$seatId}";
        Cache::put($key, true, $ttl);
    }

    public static function releaseSeat($showtimeId, $seatId)
    {
        $key = "reservation:{$showtimeId}:{$seatId}";
        Cache::forget($key);
    }
}
