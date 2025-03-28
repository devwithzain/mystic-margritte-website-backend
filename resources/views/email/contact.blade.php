<!DOCTYPE html>
<html>

<head>
   <title>New Contact Message.</title>
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
   </style>
</head>

<body>
   <div class="main">
      <div class="logo">
         <img src="{{ asset('logo.png') }}" alt="Logo">
      </div>
      <h2>New Contact Message.</h2>
      <p><strong>Name:</strong> {{ $name }}</p>
      <p><strong>Email:</strong> {{ $email }}</p>
      <p><strong>Special Message:</strong> {{ $specialMessage }}</p>
   </div>
</body>

</html>