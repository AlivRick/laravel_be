<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class TheaterRoom extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'theaterroom';
    protected $primaryKey = 'room_id';
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string'; // Định dạng kiểu string

    protected $fillable = [
        'room_id',
        'cinema_complex_id',
        'template_id',
        'room_name',
        'room_type',
        'capacity',
        'is_active'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cinemaComplex()
    {
        return $this->belongsTo(CinemaComplex::class, 'cinema_complex_id', 'cinema_complex_id');
    }

    public function seatTemplate()
    {
        return $this->belongsTo(SeatTemplate::class, 'template_id', 'template_id');
    }

    public function seats()
    {
        return $this->hasMany(Seat::class, 'room_id', 'room_id');
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'room_id', 'room_id');
    }

    public function seatChangeHistory()
    {
        return $this->hasMany(SeatChangeHistory::class, 'room_id', 'room_id');
    }
}