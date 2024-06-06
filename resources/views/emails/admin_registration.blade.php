<p>Hello{{ $admin->prenom }},</p>
<p>Your administrator account has been successfully created. Here are your login details:</p>
<ul>
    <li>Login: {{ $admin->login }}</li>
    <li>password: {{ $password }}</li>
    </ul>

