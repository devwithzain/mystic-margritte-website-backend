<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $fillable = ['user_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingDetail()
    {
        return $this->hasOne(BookingDetail::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }
}