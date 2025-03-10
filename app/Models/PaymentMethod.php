<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'paymentmethod';
    protected $primaryKey = 'payment_method_id';

    protected $fillable = [
        'method_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'payment_method_id', 'payment_method_id');
    }

    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class, 'payment_method_id', 'payment_method_id');
    }
}