<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Meeting Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #004085; /* Dark blue */
            color: white;
            padding: 10px 0;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            color: white;
            background-color: #004085; 
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Meeting Invitation</h1>
        </div>
        <div class="content">
            <p>Good morning,</p>
            <p>You are invited to participate in the meeting "{{ $reunion->titre }}".</p>
            <p>{{ $reunion->description }}</p>
            <p><strong>Date:</strong> {{ $reunion->date }}</p>
            <p>To join the meeting, please click on the link below:</p>
            <a href="http://localhost:4200/#/en-ligne?reunionId={{ $reunion->id }}&userId={{ $userId }}" class="button">Join Meeting</a>
        </div>
    </div>
</body>
</html>
