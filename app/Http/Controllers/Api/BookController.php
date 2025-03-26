<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
   public function getAllBookings()
   {
      $orders = Booking::with(['items.service', 'user', 'bookingDetail'])
         ->orderBy('created_at', 'desc')
         ->get();
      return response()->json($orders);
   }
   public function getSingleBooking($id)
   {
      $booking = Booking::with(['items.service', 'user', 'bookingDetail'])->find($id);
      if (!$booking) {
         return response()->json(['error' => 'Booking Not Found.'], 404);
      }
      return response()->json($booking);
   }
   public function getAllBookingForUser()
   {
      $user = Auth::user();
      if (!$user) {
         return response()->json(['message' => 'Unauthorized'], 401);
      }

      $bookings = Booking::where('user_id', $user->id)
         ->with(['items.service', 'user', 'bookingDetail'])
         ->orderBy('created_at', 'desc')
         ->get();

      return response()->json($bookings);
   }
   public function placeBooking(Request $request)
   {
      $request->validate([
         'user_id' => 'required|exists:users,id',
      ]);

      try {
         $booking = Booking::create([
            'user_id' => $request->user_id,
            'status' => 'pending',
         ]);

         foreach ($request->cart_items as $item) {
            BookingItem::create([
               'booking_id' => $booking->id,
               'service_id' => $item['service_id'],
            ]);
         }

         BookingDetail::create([
            'service_id' => $request->service_id,
            'booking_id' => $booking->id,
            'time_slot_id' => $request->time_slot_id,
            'birth_date' => $request->birth_date,
            'birth_time' => $request->birth_time,
            'birth_place' => $request->birth_place,
            'meeting_link' => $request->meeting_link,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country,
            'street_address' => $request->street_address,
            'town_city' => $request->town_city,
            'state' => $request->state,
            'zip' => $request->zip,
            'timezone' => $request->timezone,
            'notes' => $request->notes,
            'status' => $request->status,
         ]);


         return response()->json([
            'status' => 200,
            'message' => 'Booking placed successfully.',
            'booking_id' => $booking->id,
         ], 201);
      } catch (\Exception $e) {
         return response()->json([
            'message' => 'Failed to place the order.',
            'error' => $e->getMessage(),
         ], 500);
      }
   }
   public function updateOrderStatus(Request $request, $orderId)
   {
      $request->validate([
         'status' => 'required|in:pending,processing,paid,canceled,failed',
      ]);

      $order = Booking::find($orderId);

      if (!$order) {
         return response()->json(['message' => 'Order not found'], 404);
      }

      $order->status = $request->status;
      $order->save();

      return response()->json([
         'message' => 'Order status updated successfully',
         'order' => $order,
      ]);
   }

}