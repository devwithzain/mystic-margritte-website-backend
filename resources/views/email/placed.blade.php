<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <title>Invoice #{{ $order['id'] }}</title>
   <style>
   body {
      font-family: 'Montserrat', sans-serif;
      font-weight: 400;
      color: #322d28;
   }

   header.top-bar h1 {
      font-family: 'Montserrat', sans-serif;
   }

   main {
      margin-top: 4rem;
      min-height: calc(100vh - 107px);
   }

   .inner-container {
      max-width: 800px;
      margin: 0 auto;
   }

   table.invoice {
      background: #fff;
   }

   table.invoice .num {
      font-weight: 200;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-size: .8em;
   }

   table.invoice tr,
   table.invoice td {
      background: #fff;
      text-align: left;
      font-weight: 400;
      color: #322d28;
   }

   table.invoice tr.header td img {
      max-width: 300px;
   }

   table.invoice tr.header td h2 {
      text-align: right;
      font-family: 'Montserrat', sans-serif;
      font-weight: 200;
      font-size: 2rem;
      color: #1779ba;
   }

   table.invoice tr.intro td:nth-child(2) {
      text-align: right;
   }

   table.invoice tr.details>td {
      padding-top: 4rem;
      padding-bottom: 0;
   }

   table.invoice tr.details td.id,
   table.invoice tr.details td.qty {
      text-align: center;
   }

   table.invoice tr.details td:last-child {
      text-align: right;
   }

   table.invoice tr.details table thead:after,
   table.invoice tr.details table tbody:after {
      content: '';
      height: 1px;
      position: absolute;
      width: 100%;
      left: 0;
      margin-top: -1px;
      background: #c8c3be;
   }

   table.invoice tr.totals td {
      padding-top: 0;
   }

   table.invoice tr.totals table tr td {
      padding-top: 0;
      padding-bottom: 0;
   }

   table.invoice tr.totals table tr td:nth-child(1) {
      font-weight: 500;
   }

   table.invoice tr.totals table tr td:nth-child(2) {
      text-align: right;
      font-weight: 200;
   }

   table.invoice tr.totals table tr:nth-last-child(2) td:last-child:after {
      content: '';
      height: 4px;
      width: 110%;
      border-top: 1px solid #1779ba;
      border-bottom: 1px solid #1779ba;
      position: relative;
      right: 0;
      bottom: -.575rem;
      display: block;
   }

   table.invoice tr.totals table tr.total td {
      font-size: 1.2em;
      padding-top: .5em;
      font-weight: 700;
   }

   .additional-info h5 {
      font-size: .8em;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #1779ba;
   }
   </style>
</head>

<body>
   <div class="row expanded">
      <main class="columns">
         <div class="inner-container">
            <section class="row">
               <div class="callout large invoice-container">
                  <table class="invoice">
                     <tr class="header">
                        <td>
                           <img src={{ asset('/star.png') }} alt="Company Logo" style="width: 50px;">
                        </td>
                        <td class="align-right">
                           <h2>Invoice</h2>
                        </td>
                     </tr>
                     <tr class="intro">
                        <td>
                           Hello, {{ $order['user']['name'] }}.<br>
                           Thank you for your order.
                        </td>
                        <td class="text-right">
                           <span class="num">Order #{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</span><br>
                           {{ \Carbon\Carbon::parse($order['created_at'])->format('F d, Y') }}
                        </td>
                     </tr>
                     <tr class="details">
                        <td colspan="2">
                           <table>
                              <thead>
                                 <tr>
                                    <th class="desc">Item Description</th>
                                    <th class="id">Item ID</th>
                                    <th class="qty">Quantity</th>
                                    <th class="amt">Subtotal</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @foreach ($order['items'] as $item)
                                 <tr class="item">
                                    <td class="desc">{{ $item['product']['title'] }}</td>
                                    <td class="id num">{{ $item['product']['sku'] ?? 'N/A' }}</td>
                                    <td class="qty">{{ $item['quantity'] }}</td>
                                    <td class="amt">
                                       ${{ number_format($item['product']['price'] * $item['quantity'], 2) }}</td>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     <tr class="totals">
                        <td></td>
                        <td>
                           <table>
                              <tr class="subtotal">
                                 <td class="num">Subtotal</td>
                                 <td class="num">
                                    ${{ number_format(collect($order['items'])->sum(fn($i) => $i['product']['price'] * $i['quantity']), 2) }}
                                 </td>
                              </tr>
                              <tr class="fees">
                                 <td class="num">Shipping & Handling</td>
                                 <td class="num">
                                    ${{ number_format($order['items'][0]['product']['shipping'] ?? 0, 2) }}
                                 </td>
                              </tr>
                              <tr class="tax">
                                 <td class="num">Tax (7%)</td>
                                 <td class="num">
                                    ${{ number_format(collect($order['items'])->sum(fn($i) => $i['product']['price'] * $i['quantity']) * 0.07, 2) }}
                                 </td>
                              </tr>
                              <tr class="total">
                                 <td>Total</td>
                                 <td>
                                    ${{ number_format(
                          collect($order['items'])->sum(fn($i) => $i['product']['price'] * $i['quantity']) 
                          + ($order['items'][0]['product']['shipping'] ?? 0)
                          + collect($order['items'])->sum(fn($i) => $i['product']['price'] * $i['quantity']) * 0.07,
                          2
                        ) }}
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>

                  <section class="additional-info">
                     <div class="row">
                        <div class="columns">
                           <h5>Billing Information</h5>
                           <p>
                              {{ $order['user']['name'] }}<br>
                              <!-- You can add more user address info here if available -->
                              <!-- Example placeholder -->
                              134 Madison Ave.<br>
                              New York NY 00102<br>
                              United States
                           </p>
                        </div>
                        <div class="columns">
                           <h5>Payment Information</h5>
                           <p>
                              Credit Card<br>
                              Card Type: Visa<br>
                              &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 1234
                           </p>
                        </div>
                     </div>
                  </section>
               </div>
            </section>
         </div>
      </main>
   </div>
</body>

</html>