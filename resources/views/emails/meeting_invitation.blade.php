<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Online Meeting Invitation</title>
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
            background-color: #4CAF50;
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
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        <h1>Online Meeting Invitation</h1>
        </div>
        <div class="content">
            <p>Bonjour,</p>
            <p>You are invited to participate in the meeting"{{ $meeting['titre'] }}".</p>
            <p>{{ $meeting['description'] }}</p>
            <p><strong>Date :</strong> {{ $meeting['date'] }}</p>
            <p>To join the meeting, please click on the link below:</p>
            <a href="http://localhost:4200/#/meeting2?meetingId={{ $meeting->id }}"></a>

        </div>
    </div>
</body>
</html>

