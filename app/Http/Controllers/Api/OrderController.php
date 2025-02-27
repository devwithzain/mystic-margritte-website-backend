<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\CheckoutDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
   public function getAllOrders()
   {
      $orders = Order::with(['items.product', 'user', 'checkoutDetail'])
         ->orderBy('created_at', 'desc')
         ->get();
      return response()->json($orders);
   }

   public function getAllOrdersForUser()
   {
      $user = Auth::user();
      if (!$user) {
         return response()->json(['message' => 'Unauthorized'], 401);
      }

      $orders = Order::where('user_id', $user->id)
         ->with(['items.product', 'user', 'checkoutDetail'])
         ->orderBy('created_at', 'desc')
         ->get();

      return response()->json($orders);
   }

   public function placeOrder(Request $request)
   {
      $request->validate([
         'user_id' => 'required|exists:users,id',
         'cart_items' => 'required|array|min:1',
         'cart_items.*.product_id' => 'required|exists:products,id',
         'cart_items.*.quantity' => 'required|integer|min:1',
      ]);

      try {
         $order = Order::create([
            'user_id' => $request->user_id,
            'status' => 'pending',
         ]);

         foreach ($request->cart_items as $item) {
            OrderItem::create([
               'order_id' => $order->id,
               'product_id' => $item['product_id'],
               'quantity' => $item['quantity'],
            ]);
         }

         CheckoutDetail::create([
            'order_id' => $order->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'country' => $request->country,
            'street_address' => $request->street_address,
            'town_city' => $request->town_city,
            'state' => $request->state,
            'zip' => $request->zip,
            'agreed_terms' => $request->agreed_terms,
         ]);

         return response()->json([
            'status' => 200,
            'message' => 'Order placed successfully.',
            'order_id' => $order->id,
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

      $order = Order::find($orderId);

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