<?php

namespace App\Http\Controllers\Api;

use App\Mail\BookFormMail;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
   public function placeBooking(Request $request, ZoomService $zoom)
   {
      $request->validate([
         'user_id' => 'required|exists:users,id',
         'service_id' => 'required|exists:services,id',
         'time_slot_id' => 'required|exists:time_slots,id',
         'first_name' => 'required|string',
         'last_name' => 'required|string',
         'phone' => 'required|string',
         'email' => 'required|email',
         'birth_date' => 'required|date',
         'birth_time' => 'required',
         'birth_place' => 'required|string',
         'country' => 'nullable|string',
         'street_address' => 'nullable|string',
         'town_city' => 'nullable|string',
         'state' => 'nullable|string',
         'zip' => 'nullable|string',
         'timezone' => 'nullable|string',
         'notes' => 'nullable|string',
         'status' => 'nullable|string',
      ]);

      try {
         // create booking
         $booking = Booking::create([
            'user_id' => $request->user_id,
            'status' => $request->status ?? 'pending',
         ]);

         // store booking items
         if (!empty($request->cart_items)) {
            foreach ($request->cart_items as $item) {
               BookingItem::create([
                  'booking_id' => $booking->id,
                  'service_id' => $item['service_id'],
                  'quantity' => $item['quantity'] ?? 1,
                  'price' => $item['price'] ?? 0,
               ]);
            }
         }

         // store booking detail
         $detail = BookingDetail::create([
            'service_id' => $request->service_id,
            'booking_id' => $booking->id,
            'time_slot_id' => $request->time_slot_id,
            'birth_date' => $request->birth_date,
            'birth_time' => $request->birth_time,
            'birth_place' => $request->birth_place,
            'meeting_link' => null,
            'zoom_meeting_id' => null,
            'meeting_start_url' => null,
            'meeting_password' => null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country ?? null,
            'street_address' => $request->street_address ?? null,
            'town_city' => $request->town_city ?? null,
            'state' => $request->state ?? null,
            'zip' => $request->zip ?? null,
            'timezone' => $request->timezone ?? 'Asia/Karachi',
            'notes' => $request->notes ?? null,
            'status' => $request->status ?? 'scheduled',
         ]);

         // mark timeslot as booked
         $timeSlot = TimeSlot::find($request->time_slot_id);
         if ($timeSlot) {
            $timeSlot->update(['status' => 'booked']);
         }

         // --- CREATE ZOOM MEETING ---
         if ($timeSlot) {
            $tz = $detail->timezone ?? 'Asia/Karachi';
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $timeSlot->date . ' ' . $timeSlot->start_time, $tz);

            $zoomPayload = [
               'topic' => 'Astrology Consultation: ' . $detail->first_name . ' ' . $detail->last_name,
               'type' => 2,
               'start_time' => $startDateTime->toIso8601String(),
               'duration' => $request->duration_minutes ?? 60,
               'timezone' => $tz,
               'password' => substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8),
               'settings' => [
                  'host_video' => true,
                  'participant_video' => true,
                  'join_before_host' => false,
                  'approval_type' => 0
               ],
            ];

            try {
               $userId = config('services.zoom.default_user');
               $zoomResp = $zoom->createMeeting($userId, $zoomPayload);

               // update booking detail with Zoom info
               $detail->update([
                  'meeting_link' => $zoomResp['join_url'] ?? null,
                  'zoom_meeting_id' => $zoomResp['id'] ?? null,
                  'meeting_start_url' => $zoomResp['start_url'] ?? null,
                  'meeting_password' => $zoomResp['password'] ?? $zoomPayload['password'],
               ]);
            } catch (\Throwable $e) {
               $detail->update(['status' => 'zoom_failed']);
               Log::error('Zoom create meeting failed: ' . $e->getMessage());
            }
         }

         // --- SEND EMAILS ---
         $mailData = [
            'first_name' => $detail->first_name,
            'last_name' => $detail->last_name,
            'email' => $detail->email,
            'phone' => $detail->phone,
            'birth_date' => $detail->birth_date,
            'birth_time' => $detail->birth_time,
            'birth_place' => $detail->birth_place,
            'timezone' => $detail->timezone,
            'time_slot_id' => $detail->time_slot_id,
            'meeting_link' => $detail->meeting_link,
            'meeting_start_url' => $detail->meeting_start_url,
            'notes' => $detail->notes,
            'state' => $detail->state,
            'town_city' => $detail->town_city,
            'street_address' => $detail->street_address,
            'country' => $detail->country,
         ];

         $subjectToAdmin = "New Booking: {$detail->first_name} {$detail->last_name}";
         $subjectToClient = "Your Booking Confirmation - Mystice Marguerite";

         try {
            Mail::to(config('mail.from.address'))->send(new BookFormMail($subjectToAdmin, data: $mailData));
            Mail::to($detail->email)->send(new BookFormMail($subjectToClient, data: $mailData));
         } catch (\Throwable $e) {
            Log::error('Booking mail send failed: ' . $e->getMessage());
         }

         return response()->json([
            'status' => 201,
            'message' => 'Booking placed successfully.',
            'booking_id' => $booking->id,
            'booking' => $booking,
            'detail' => $detail,
         ], 201);
      } catch (\Exception $e) {
         return response()->json([
            'message' => 'Failed to place the booking.',
            'error' => $e->getMessage(),
         ], 500);
      }
   }

   public function updateBookingStatus(Request $request, $bookid)
   {
      $request->validate([
         'status' => 'required|in:pending,processing,paid,canceled,failed',
      ]);

      $booking = Booking::find($bookid);

      if (!$booking) {
         return response()->json(['message' => 'Booking not found'], 404);
      }

      $booking->status = $request->status;
      $booking->save();

      return response()->json([
         'message' => 'booking status updated successfully',
         'booking' => $booking,
      ]);
   }
}
