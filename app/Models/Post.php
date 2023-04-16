<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable =
    [
 
        'title',
        'body',
       'created_by',
       'views',
       'image',
       'is_pinned',
       
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];





    //   relasi
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id',);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
    public function postsaves()
    {
        return $this->hasMany(PostSave::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function images()
    {
        return $this->belongsTo(Post::class,'image');
    }

}
