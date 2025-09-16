<?php

namespace App\Http\Controllers\Api;

use Square\SquareClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Square\Legacy\Models\Money;
use App\Http\Controllers\Controller;
use Square\Payments\Requests\CreatePaymentRequest;

class SquarePaymentController extends Controller
{
   protected function squareClient(): SquareClient
   {
      return new SquareClient(
         token: env('SQUARE_ACCESS_TOKEN'),
         options: env('SQUARE_ENVIRONMENT'),
      );
   }

   public function pay(Request $request)
   {
      try {
         $sourceId = $request->input('sourceId');
         $amount = $request->input('amount', 100);

         $money = new Money(
            amount: (int) $amount,
            currency: 'USD'
         );

         $paymentRequest = new CreatePaymentRequest(
            amount: $money
         );

         $response = $this->squareClient()->getPaymentsApi()->createPayment($paymentRequest);

         if ($response->isSuccess()) {
            return response()->json([
               'success' => true,
               'payment' => $response->getResult()->getPayment(),
            ]);
         }

         return response()->json([
            'success' => false,
            'errors' => $response->getErrors(),
         ], 400);
      } catch (\Exception $e) {
         return response()->json([
            'success' => false,
            'message' => 'Payment failed',
            'error' => $e->getMessage(),
         ], 500);
      }
   }
}
