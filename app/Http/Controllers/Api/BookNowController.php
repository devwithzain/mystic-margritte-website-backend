<?php

namespace App\Http\Controllers\Api;

use App\Mail\BookNowFormMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BookNowRequest;

class BookNowController extends Controller
{
   public function sendBookForm(BookNowRequest $request)
   {
      $data = $request->validated();
      $subject = "New Booking from " . $data['name'];

      try {
         Mail::to(config('mail.from.address'))->send(new BookNowFormMail($subject, data: $data));
      } catch (\Exception $e) {
         return response()->json(['error' => 'Failed to booking. Please try again later.'], 500);
      }
      return response()->json(['success' => "Your booking has been submitted successfully."], 200);
   }
}