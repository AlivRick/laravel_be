<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatTemplate extends Model
{
    use HasFactory;

    protected $table = 'seattemplate';
    protected $primaryKey = 'template_id';

    protected $fillable = [
        'template_name',
        'description',
        'total_rows',
        'seats_per_row'
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'seats_per_row' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function theaterRooms()
    {
        return $this->hasMany(TheaterRoom::class, 'template_id', 'template_id');
    }
}