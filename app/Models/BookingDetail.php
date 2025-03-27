<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Traits\GeneratesId;
class BookingDetail extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'bookingdetail';
    protected $primaryKey = 'booking_detail_id';
    public $incrementing = false; // Không tự tăng ID
    protected $keyType = 'string'; // Định dạng kiểu chuỗi

    protected $fillable = [
        'booking_detail_id',
        'booking_id',
        'showtime_id',
        'seat_id',
        'ticket_type_id',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class, 'showtime_id', 'showtime_id');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seat_id', 'seat_id');
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id', 'ticket_type_id');
    }
}