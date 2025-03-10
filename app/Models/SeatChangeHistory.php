<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatChangeHistory extends Model
{
    use HasFactory;

    protected $table = 'seatchangehistory';
    protected $primaryKey = 'history_id';
    public $timestamps = false;

    protected $fillable = [
        'seat_id',
        'room_id',
        'changed_by',
        'previous_state',
        'current_state',
        'change_reason'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seat_id', 'seat_id');
    }

    public function theaterRoom()
    {
        return $this->belongsTo(TheaterRoom::class, 'room_id', 'room_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by', 'user_id');
    }
}