<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class Genre extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'genre';
    protected $primaryKey = 'genre_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'genre_id',
        'genre_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'moviegenre', 'genre_id', 'movie_id');
    }
    // Scope to get only non-deleted genres
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }
}