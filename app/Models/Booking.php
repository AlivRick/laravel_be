<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class Booking extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'booking';
    protected $primaryKey = 'booking_id';
    public $incrementing = false; // Không tự tăng ID
    protected $keyType = 'string'; // Định dạng kiểu chuỗi

    protected $fillable = [
        'booking_id',
        'user_id',
        'booking_time',
        'total_amount',
        'payment_method_id',
        'payment_status',
        'booking_status'
    ];

    protected $casts = [
        'booking_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'payment_method_id');
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'booking_id', 'booking_id');
    }

    public function concessions()
    {
        return $this->hasMany(BookingConcession::class, 'booking_id', 'booking_id');
    }

    public function paymentHistory()
    {
        return $this->hasMany(PaymentHistory::class, 'booking_id', 'booking_id');
    }
}