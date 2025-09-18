<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
   public function store(Request $request)
   {
      $request->validate([
         'service_id' => 'required|exists:services,id',
         'time_slot_id' => 'required|exists:time_slots,id',
      ]);

      $timeSlot = TimeSlot::findOrFail($request->time_slot_id);
      if ($timeSlot->status === 'booked') {
         return response()->json(['error' => 'Time slot already booked'], 400);
      }

      $timeSlot->update(['status' => 'booked']);

      $booking = Booking::create([
         'user_id' => Auth::id(),
         'service_id' => $request->service_id,
         'time_slot_id' => $request->time_slot_id,
         'meeting_link' => "https://zoom.us/meeting/" . uniqid(),
         'status' => 'confirmed',
      ]);

      return response()->json(['success' => 'Booking successful', 'booking' => $booking]);
   }

   public function index()
   {
      $bookings = Booking::where('user_id', Auth::id())->with('service', 'timeSlot')->get();
      return response()->json(['bookings' => $bookings]);
   }
}