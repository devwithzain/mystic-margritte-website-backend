<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $table = 'time_slots';
    protected $fillable = ['date', 'start_time', 'end_time', 'status'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}