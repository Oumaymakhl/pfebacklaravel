<div style="border: 1px solid #ccc; padding: 10px; width: 300px;">
    <div style="background-color: #004085; color: #fff; padding: 10px;">
        <h2 style="color: white;">Admin Registration</h2>
    </div>
    <div style="padding: 10px;">
    <p>Hello{{ $admin->prenom }},</p>
<p>Your administrator account has been successfully created. Here are your login details:</p>
<ul>
    <li>Login: {{ $admin->login }}</li>
    <li>Mot de passe: {{ $password }}</li>
    </ul>
    </div>
</div>