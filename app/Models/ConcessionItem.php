<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class ConcessionItem extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'concessionitem';
    protected $primaryKey = 'item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'item_id',
        'item_name',
        'description',
        'price',
        'image',
        'category',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bookingConcessions()
    {
        return $this->hasMany(BookingConcession::class, 'item_id', 'item_id');
    }
}