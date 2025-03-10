<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $table = 'tickettype';
    protected $primaryKey = 'ticket_type_id';

    protected $fillable = [
        'type_name',
        'description',
        'discount_percentage',
        'is_active'
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class, 'ticket_type_id', 'ticket_type_id');
    }
}