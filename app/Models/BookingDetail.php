<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'booking_id',
        'booking_id',
        'time_slot_id',
        'birth_date',
        'birth_time',
        'birth_place',
        'meeting_link',
        'first_name',
        'last_name',
        'phone',
        'email',
        'country',
        'street_address',
        'town_city',
        'state',
        'zip',
        'timezone',
        'notes',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}