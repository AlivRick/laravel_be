<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class CinemaComplex extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'cinemacomplex';
    protected $primaryKey = 'cinema_complex_id';
    public $incrementing = false; // Không tự tăng ID
    protected $keyType = 'string'; // Định dạng kiểu chuỗi

    protected $fillable = [
        'cinema_complex_id',
        'complex_name',
        'address',
        'city',
        'province',
        'phone_number',
        'email',
        'opening_time',
        'closing_time',
        'description',
        'image',
        'is_active'
    ];

    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function theaterRooms()
    {
        return $this->hasMany(TheaterRoom::class, 'cinema_complex_id', 'cinema_complex_id');
    }
}