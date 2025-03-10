<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $table = 'movie';
    protected $primaryKey = 'movie_id';

    protected $fillable = [
        'title',
        'original_title',
        'director',
        'cast',
        'description',
        'duration',
        'release_date',
        'end_date',
        'country',
        'language',
        'age_restriction',
        'trailer_url',
        'poster_url',
        'is_active'
    ];

    protected $casts = [
        'duration' => 'integer',
        'release_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'moviegenre', 'movie_id', 'genre_id');
    }

    public function reviews()
    {
        return $this->hasMany(MovieReview::class, 'movie_id', 'movie_id');
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'movie_id', 'movie_id');
    }
}