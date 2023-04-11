<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
        'description'

    
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
