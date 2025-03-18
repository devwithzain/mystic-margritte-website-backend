<?php

namespace App\Http\Controllers\Api;

use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeSlotController extends Controller
{
   public function index()
   {
      $timeslots = TimeSlot::all();
      return response()->json([
         'timeslots' => $timeslots
      ], 200);
   }
   public function availableSlots()
   {
      $timeslots = TimeSlot::where('status', 'available')->get();
      return response()->json([
         'timeslots' => $timeslots
      ], 200);
   }
   public function store(Request $request)
   {
      $validatedData = $request->validate([
         'user_id' => 'required|exists:users,id',
         'date' => 'required|string',
         'start_time' => 'required|date_format:H:i:s',
         'end_time' => 'required|date_format:H:i:s',
         'status' => 'required|in:available,unavailable,booked'
      ]);

      TimeSlot::create([
         'user_id' => $validatedData['user_id'],
         'date' => $validatedData['date'],
         'start_time' => $validatedData['start_time'],
         'end_time' => $validatedData['end_time'],
         'status' => $validatedData['status']
      ]);

      return response()->json(['message' => 'Time slot created successfully!'], 201);
   }
   public function show($id)
   {
      $timeSlot = TimeSlot::findOrFail($id);
      return response()->json([
         'timeslot' => $timeSlot
      ], 200);
   }
   public function update(Request $request, $id)
   {
      $validatedData = $request->validate([
         'date' => 'nullable|string',
         'start_time' => 'nullable|date_format:H:i:s',
         'end_time' => 'nullable|date_format:H:i:s',
         'status' => 'nullable|in:available,unavailable,booked'
      ]);

      $timeSlot = TimeSlot::findOrFail($id);

      if ($request->has('date')) {
         $timeSlot->date = $validatedData['date'];
      }
      if ($request->has('start_time')) {
         $timeSlot->start_time = $validatedData['start_time'];
      }
      if ($request->has('end_time')) {
         $timeSlot->end_time = $validatedData['end_time'];
      }
      if ($request->has('status')) {
         $timeSlot->status = $validatedData['status'];
      }

      $timeSlot->save();

      return response()->json(['message' => 'Time slot updated successfully!']);
   }
   public function destroy($id)
   {
      $timeSlot = TimeSlot::findOrFail($id);
      $timeSlot->delete();

      return response()->json(['message' => 'Time slot deleted successfully!']);
   }
}