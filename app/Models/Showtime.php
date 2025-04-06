<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class Showtime extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'showtime';
    protected $primaryKey = 'showtime_id';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false; // Không dùng auto-increment

    protected $fillable = [
        'showtime_id',
        'movie_id',
        'room_id',
        'start_time',
        'end_time',
        'price',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'movie_id');
    }

    public function theaterRoom()
    {
        return $this->belongsTo(TheaterRoom::class, 'room_id', 'room_id');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'showtime_id', 'showtime_id');
    }
}
