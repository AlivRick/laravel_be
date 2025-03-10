<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotion';
    protected $primaryKey = 'promotion_id';

    protected $fillable = [
        'promotion_name',
        'description',
        'discount_amount',
        'discount_percentage',
        'start_date',
        'end_date',
        'promotion_code',
        'min_purchase',
        'max_discount',
        'is_active'
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'userpromotion', 'promotion_id', 'user_id')
                    ->withPivot('is_used', 'used_at');
    }
}