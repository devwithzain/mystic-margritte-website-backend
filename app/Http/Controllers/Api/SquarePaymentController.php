<?php

namespace App\Http\Controllers\Api;

use Square\Types\Money;
use Square\SquareClient;
use Square\Types\Currency;
use Illuminate\Http\Request;
use Square\Types\CashPaymentDetails;
use App\Http\Controllers\Controller;
use Square\Payments\Requests\CreatePaymentRequest;

class SquarePaymentController extends Controller
{
   public function pay(Request $request)
   {
      try {
         $validated = $request->validate([
            'sourceId' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'buyerEmail' => 'required|email',
         ]);

         $square = new SquareClient("SQUARE_ACCESS_TOKEN");
         $response = $square->payments->create(
            request: new CreatePaymentRequest(
               [
                  'idempotencyKey' => '4935a656-a929-4792-b97c-8848be85c27c',
                  'sourceId' => $validated['sourceId'],
                  'amountMoney' => new Money([
                     'amount' => $validated['amount'],
                     'currency' => Currency::from($validated['currency'])->value
                  ]),
                  'cashDetails' => new CashPaymentDetails([
                     'buyerSuppliedMoney' => new Money([
                        'amount' => $validated['amount'],
                        'currency' => Currency::from($validated['currency'])->value
                     ])
                  ]),
               ],
            )
         );

         return response()->json(['errors' => $response->getErrors()], 400);

      } catch (\Exception $e) {
         return response()->json([
            'error' => $e->getMessage()
         ], 500);
      }
   }
}