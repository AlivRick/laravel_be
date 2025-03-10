<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $table = 'seat';
    protected $primaryKey = 'seat_id';

    protected $fillable = [
        'room_id',
        'seat_row',
        'seat_number',
        'seat_type',
        'is_available',
        'is_active',
        'is_merged',
        'merged_with_seat_id'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'is_merged' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function theaterRoom()
    {
        return $this->belongsTo(TheaterRoom::class, 'room_id', 'room_id');
    }

    public function mergedSeat()
    {
        return $this->belongsTo(Seat::class, 'merged_with_seat_id', 'seat_id');
    }

    public function mergedSeats()
    {
        return $this->hasMany(Seat::class, 'merged_with_seat_id', 'seat_id');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'seat_id', 'seat_id');
    }

    public function seatChangeHistory()
    {
        return $this->hasMany(SeatChangeHistory::class, 'seat_id', 'seat_id');
    }
}