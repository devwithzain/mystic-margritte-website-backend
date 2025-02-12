<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'services';
    protected $fillable = ['title', 'description', 'short_description', 'image'];

}