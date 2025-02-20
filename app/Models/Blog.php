<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $fillable = ['title', 'description', 'short_description', 'image'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}