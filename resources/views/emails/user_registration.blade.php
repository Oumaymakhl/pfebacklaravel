<!-- resources/views/emails/user_registration.blade.php -->
<<<<<<< HEAD
<p>Hello {{ $user->prenom }},</p>
<p>Your user account has been successfully created. Here are your login details:</p>
<ul>
    <li>Login: {{ $user->login }}</li>
    <li>password: {{ $password }}</li>
</ul>


=======
<div style="border: 1px solid #ccc; padding: 10px; width: 300px;">
    <div style="background-color: #004085; color: #fff; padding: 10px;">
        <h2 style="color: white;">User Registration</h2>
    </div>
    <div style="padding: 10px;">
        <p>Hello {{ $user->prenom }},</p>
        <p>Your user account has been successfully created. Here are your login details:</p>
        <ul>
            <li>Login: {{ $user->login }}</li>
            <li>Mot de passe: {{ $password }}</li>
        </ul>
    </div>
</div>
>>>>>>> 448a390ada3909ef35008b22ac4459f52cd2ec84
