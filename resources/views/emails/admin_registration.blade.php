<!-- resources/views/emails/admin_registration.blade.php -->
<p>Bonjour {{ $admin->prenom }},</p>
<p>Votre compte administrateur a été créé avec succès. Voici vos informations de connexion :</p>
<ul>
    <li>Login: {{ $admin->login }}</li>
    <li>Mot de passe: {{ $password }}</li>
</ul>
<p>Merci de vous être inscrit.</p>
