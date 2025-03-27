<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GeneratesId;
class Role extends Model
{
    use HasFactory, GeneratesId;

    protected $table = 'role';
    protected $primaryKey = 'role_id';
    public $incrementing = false; // Không dùng auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'role_id',
        'role_name',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}