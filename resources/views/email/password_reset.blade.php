<!DOCTYPE html>
<html>

<head>
   <title>Password Reset Code</title>
   <style>
      body {
         font-family: sans-serif, Arial, ;
         line-height: 1.6;
         max-width: 600px;
         margin: 0 auto;
         padding: 20px;
         background-color: #f5f5f5;
      }

      h2 {
         color: #333;
         border-bottom: 2px solid #ddd;
         padding-bottom: 10px;
      }

      h3 {
         background-color: #fff;
         padding: 15px;
         border-radius: 5px;
         text-align: center;
         font-size: 24px;
         color: #2c3e50;
         border: 1px solid #ddd;
      }

      p {
         color: #666;
         margin: 15px 0;
      }
   </style>
</head>

<body>
   <h2>Password Reset Code</h2>
   <p>Your 6-digit password reset code is:</p>
   <h3>{{ $code }}</h3>
   <p>This code will expire in 10 minutes.</p>
   <p>If you didn't request this, you can ignore this email.</p>
</body>

</html>