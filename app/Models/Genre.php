<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $table = 'genre';
    protected $primaryKey = 'genre_id';

    protected $fillable = [
        'genre_name',
        'description',
        'is_deleted'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_deleted' => 'boolean'
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