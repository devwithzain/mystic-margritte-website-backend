<!DOCTYPE html>
<html>

<head>
   <title>New Message.</title>
   <style>
      body {
         font-family: sans-serif, Arial;
         line-height: 1.6;
         margin: 20px;
         color: #333;
      }

      h2 {
         color: #2c3e50;
         border-bottom: 2px solid #eee;
         padding-bottom: 10px;
      }

      p {
         margin: 10px 0;
      }

      strong {
         color: #2c3e50;
      }
   </style>
</head>

<body>
   <h2>New Message.</h2>
   <p><strong>Name:</strong> {{ $name }}</p>
   <p><strong>Email:</strong> {{ $email }}</p>
   <p><strong>Special Message:</strong> {{ $specialMessage }}</p>
</body>

</html>