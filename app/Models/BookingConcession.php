<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingConcession extends Model
{
    use HasFactory;

    protected $table = 'bookingconcession';
    protected $primaryKey = 'booking_concession_id';

    protected $fillable = [
        'booking_id',
        'item_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function concessionItem()
    {
        return $this->belongsTo(ConcessionItem::class, 'item_id', 'item_id');
    }
}