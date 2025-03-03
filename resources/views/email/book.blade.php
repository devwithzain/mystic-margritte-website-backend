<!DOCTYPE html>
<html>

<head>
   <title>New Booking.</title>
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
   <h2>New Booking.</h2>
   <p><strong>Name:</strong> {{ $name }}</p>
   <p><strong>Last Name:</strong> {{ $lastName }}</p>
   <p><strong>Services:</strong> {{ $services }}</p>
   <p><strong>Healing Topics:</strong> {{ $healingTopics }}</p>
   <p><strong>Preferred Time:</strong> {{ $preferredTime }}</p>
   <p><strong>City And State:</strong> {{ $cityAndState }}</p>
   <p><strong>Special Message:</strong> {{ $specialMessage }}</p>
</body>

</html>