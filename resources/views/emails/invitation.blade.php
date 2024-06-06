<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to the meeting</title>
</head>
<body>
    
    <p>You are invited to participate in the meeting  "{{ $reunion->titre }}".</p>
    <p>Date: {{ $reunion->date }}</p>
    <p>Description: {{ $reunion->description }}</p>

    <p>Confirm your participation by clicking on the following link:</p>
    <a href="http://localhost:4200/#/en-ligne?reunionId={{ $reunion->id }}&userId={{ $userId }}">Confirm participation</a>
</body>
</html>