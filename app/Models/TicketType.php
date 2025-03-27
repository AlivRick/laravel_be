<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class TicketType extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'tickettype';
    protected $primaryKey = 'ticket_type_id';
    public $incrementing = false; // Không tự tăng ID
    protected $keyType = 'string'; // Định dạng kiểu chuỗi

    protected $fillable = [
        'ticket_type_id',
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