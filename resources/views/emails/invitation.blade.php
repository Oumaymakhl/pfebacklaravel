<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Invitation</title>
</head>
<body>
    <h2>Meeting Invitation</h2>
    <p>You are invited to participate in the meeting "{{ $meeting->title }}".</p>
    <p>Date: {{ $meeting->date }}</p>
    <p>Description: {{ $meeting->description }}</p>

    <p>Confirm your participation by clicking on the following link:</p>
    <a href="http://localhost:4200/#/en-ligne?reunionId={{ $reunion->id }}&userId={{ $userId }}">Confirm Participation</a>
</body>
</html>
