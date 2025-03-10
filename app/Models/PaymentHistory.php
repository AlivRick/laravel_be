<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'paymenthistory';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'booking_id',
        'payment_method_id',
        'amount',
        'transaction_id',
        'payment_time',
        'payment_status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'payment_method_id');
    }
}