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
      width: 700px;
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
         <p><strong>First Name:</strong> </p>
         <p><strong>Last Name:</strong> </p>
         <p><strong>Email:</strong> </p>
         <p><strong>Phone:</strong> </p>
         <p><strong>Country:</strong> </p>
         <p><strong>Street Address:</strong> </p>
         <p><strong>Town / City:</strong> </p>
         <p><strong>State:</strong> </p>
         <p><strong>ZIP Code:</strong> </p>
         <p><strong>Birth Date:</strong> </p>
         <p><strong>Birth Time:</strong> </p>
         <p><strong>Birth Place:</strong> </p>
         <p><strong>Timezone:</strong> </p>
         <p><strong>Time Slot ID:</strong> </p>
      </div>
      <p><strong>Meeting Link:</strong> <a href="">asd</a></p>
      <p><strong>Notes:</strong> asdas?? 'No additional notes provided.' </p>
   </div>
</body>

</html>