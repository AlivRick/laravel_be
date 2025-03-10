<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcessionItem extends Model
{
    use HasFactory;

    protected $table = 'concessionitem';
    protected $primaryKey = 'item_id';

    protected $fillable = [
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