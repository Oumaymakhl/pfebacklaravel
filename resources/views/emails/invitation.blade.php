<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation à la réunion</title>
</head>
<body>
    <h2>Invitation à la réunion</h2>
    <p>Vous êtes invité à participer à la réunion "{{ $reunion->titre }}".</p>
    <p>Date: {{ $reunion->date }}</p>
    <p>Description: {{ $reunion->description }}</p>
    <p>Confirmez votre participation en cliquant sur le lien suivant:</p>
    <a href="{{ url('/confirm-participation') }}">Confirmer la participation</a>
</body>
</html>
