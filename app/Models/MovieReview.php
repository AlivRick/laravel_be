<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class MovieReview extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'moviereview';
    protected $primaryKey = 'review_id';
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'review_id',
        'movie_id',
        'user_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id', 'movie_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}