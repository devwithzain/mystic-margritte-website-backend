<!DOCTYPE html>
<html>

<head>
   <title>Confirm Your Subscription</title>
   <style>
   * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
   }

   .main {
      width: 500px;
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

   a {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      padding: 10px 20px;
      border-radius: 5px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      font-size: 18px;
   }

   a:hover {
      background-color: #0056b3;
   }
   </style>
</head>

<body>
   <div class="main">
      <div class="logo">
         <img src="{{ asset('logo.png') }}" alt="Logo">
      </div>
      <h2>Thank you for subscribing!</h2>
      <p>Please confirm your subscription by clicking the link below:</p>
      <a href="{{ $verificationUrl }}">Confirm Subscription</a>
      <p>If you didn't request this, you can ignore this email.</p>
   </div>
</body>

</html>