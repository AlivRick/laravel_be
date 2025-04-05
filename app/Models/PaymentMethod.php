<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class PaymentMethod extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'paymentmethod';
    protected $primaryKey = 'payment_method_id';
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'payment_method_id',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}