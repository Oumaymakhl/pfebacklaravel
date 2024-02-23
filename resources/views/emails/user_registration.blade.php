<!-- resources/views/emails/user_registration.blade.php -->
<p>Bonjour {{ $user->prenom }},</p>
<p>Votre compte utilisateur a été créé avec succès. Voici vos informations de connexion :</p>
<ul>
    <li>Login: {{ $user->login }}</li>
    <li>Mot de passe: {{ $password }}</li>
</ul>

<p>Merci de vous être inscrit.</p>
