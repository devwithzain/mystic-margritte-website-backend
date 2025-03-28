<!DOCTYPE html>
<html>

<head>
   <title>New Booking Request</title>
   <style>
   * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
   }

   .main {
      width: 100%;
      padding: 40px;
      border-radius: 20px;
      background-color: #000;
      font-family: sans-serif;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 24px;
   }

   h2 {
      color: #fff;
      font-size: 30px;
      border-bottom: 2px solid #ddd;
      width: fit-content;
   }

   h3 {
      background-color: #fff;
      padding: 15px;
      border-radius: 5px;
      text-align: center;
      font-size: 24px;
      color: #000;
      border: 1px solid #ddd;
   }


   p {
      color: #fff;
      font-size: 18px;
   }

   img {
      width: 100px;
      height: 100px;
      object-fit: cover;
   }

   .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
   }

   .meet {
      display: flex;
      gap: 6px;
   }

   .data-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
   }
   </style>
</head>

<body>

   <div class="main">
      <div class="logo">
         <img src="{{ asset('logo.png') }}" alt="Logo">
      </div>
      <h2>New Booking Request</h2>
      <div class="data-grid">
         <p><strong>First Name:</strong> {{ $data['first_name'] }}</p>
         <p><strong>Last Name:</strong> {{ $data['last_name'] }}</p>
         <p><strong>Email:</strong> {{ $data['email'] }}</p>
         <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
         <p><strong>Country:</strong> {{ $data['country'] }}</p>
         <p><strong>Street Address:</strong> {{ $data['street_address'] }}</p>
         <p><strong>Town / City:</strong> {{ $data['town_city'] }}</p>
         <p><strong>State:</strong> {{ $data['state'] }}</p>
         <p><strong>ZIP Code:</strong> {{ $data['zip'] }}</p>
         <p><strong>Birth Date:</strong> {{ $data['birth_date'] }}</p>
         <p><strong>Birth Time:</strong> {{ $data['birth_time'] }}</p>
         <p><strong>Birth Place:</strong> {{ $data['birth_place'] }}</p>
         <p><strong>Timezone:</strong> {{ $data['timezone'] }}</p>
         <p><strong>Time Slot ID:</strong> {{ $data['time_slot_id'] }}</p>
      </div>
      @if(!empty($data['meeting_link']))
      <p class="meet"><strong>Meeting Link:</strong> <a
            href="{{ $data['meeting_link'] }}">{{ $data['meeting_link'] }}</a></p>@endif
      <p><strong>Notes:</strong> {{ $data['notes'] ?? 'No additional notes provided.' }}</p>
</body>

</html>