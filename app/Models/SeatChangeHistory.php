<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class SeatChangeHistory extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'seatchangehistory';
    protected $primaryKey = 'history_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'history_id',
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