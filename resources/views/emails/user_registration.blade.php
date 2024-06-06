<!-- resources/views/emails/user_registration.blade.php -->
<p>Hello {{ $user->prenom }},</p>
<p>Your user account has been successfully created. Here are your login details:</p>
<ul>
    <li>Login: {{ $user->login }}</li>
    <li>password: {{ $password }}</li>
</ul>


