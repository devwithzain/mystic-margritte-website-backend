<!DOCTYPE html>
<html>

<head>
   <title>Confirm Your Subscription</title>
   <style>
      body {
         font-family: sans-serif, Arial;
         line-height: 1.6;
         max-width: 600px;
         margin: 0 auto;
         padding: 20px;
         background-color: #f5f5f5;
      }

      h2 {
         color: #333;
         text-align: center;
         margin-bottom: 20px;
      }

      p {
         color: #666;
         margin-bottom: 15px;
      }

      a {
         display: inline-block;
         background-color: #007bff;
         color: white;
         text-decoration: none;
         padding: 10px 20px;
         border-radius: 5px;
         margin: 15px 0;
      }

      a:hover {
         background-color: #0056b3;
      }
   </style>
</head>

<body>
   <h2>Thank you for subscribing!</h2>
   <p>Please confirm your subscription by clicking the link below:</p>
   <a href="{{ $verificationUrl }}">Confirm Subscription</a>
   <p>If you didn't request this, you can ignore this email.</p>
</body>

</html>