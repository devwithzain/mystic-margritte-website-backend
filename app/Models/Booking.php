<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = ['user_id', 'service_id', 'time_slot_id', 'meeting_link', 'status'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}