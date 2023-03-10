<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class comment extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'post_id',
        'content',
    ];




    // relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }


// mutator waktu
protected $appends = ['created_at_parse','update_at_parse'];


public function createdAtParse(): Attribute
{
    return Attribute::make(
        get: fn ($value) => Carbon::parse($value)->translatedFormat("d F Y"),
    );
}

public function updateAtParse(): Attribute
{
    return Attribute::make(
        get: fn ($value) => Carbon::parse($value)->translatedFormat("d F Y"),
    );
}


}
