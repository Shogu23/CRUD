<?php
// On importe les fichiers nécessaires

use PHPMailer\PHPMailer\Exception;

require_once 'inc/header.php';

require_once 'inc/nav.php';

// On vérifie si on a posté le formulaire
if(!empty($_POST)){
    // On vérifie que tout les champs obligatoires sont remplis
    if(isset($_POST['email']) && !empty($_POST['email'])){
        // On vérifie que l'e-mail est valide (format)
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $_SESSION['message'][] = 'email invalide';
            header ('Location: oublie-pass.php');
        } 
        // On récupère l'e-mail
        $email = $_POST['email'];

        // On va chercher dans la base l(utilisateur possèdant cette adresse e-mail)
        // On se connecte à la base
        require_once 'inc/connect.php';
        
        // On écrit la requête
        $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs
        $query->bindValue(':email', $email, PDO::PARAM_STR);

        // On exécute la query
        $query->execute();

        // On vérifie si on a une réponse
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if(!$user){
            $_SESSION['message'][] = 'Si le compte concerné existe, vous recevrez un email de réinitialisation de mot de passe';
            header('Location: ' . URL);
            exit;
        }

        //Ici un utilisateur ayant l'adresse email entrée a été trouvé
        $token = md5(uniqid());
        $expiration = date('Y-m-d H:i:s', strtotime("+1 hour"));
        
        // On écrit la requête
        $sql = "UPDATE `users` SET `reset_token` = '$token', `expiration_date` = '$expiration' WHERE `email` = '{$user['email']}' ";

        // On exécute la requête
        $query = $db->query($sql);

        require_once 'inc/config-mail.php';
        
        // Ce fichier enverra un mail dès son chargement
        try{
            // On définit l'expéditeur du mail
            $sendmail->setFrom('no-reply@mondommaine.fr', 'Blog');

            // On définit le/les destinataire(s)
            $sendmail->addAddress($user['email'], $user['nickname']);

            // On définit le sujet du mail
            $sendmail->Subject = 'Votre demande de réinitialisation de mot de passe';

            // On active le HTML
            $sendmail->isHTML();

            // On écrit le contenu du mail
            // En HTML
            $sendmail->Body = "<h1>Réinitialisation MdP</h1>
                               <p>Une demande de réinitialisation de mdp a été effectué sur le super blog</p>
                               <p>Si vous n'êtes pas à l'origine de cette demande veuillez ignorer ce message</p>
                               <p>Dans le cas contraire, veuillez cliquer sur le lien ci-dessous. Celui-ci expirera après 1 heure.</p>
                               <p><a href='" . URL . "/reset-pass.php?token=$token'>" . URL . "/reset-pass.php?token=$token</a></p>";

            // En texte brut
            $sendmail->AltBody = "Réinitialisation MdP\n
            Une demande de réinitialisation de mdp a été effectué sur le super blog\n
            Si vous n'êtes pas à l'origine de cette demande veuillez ignorer ce message\n
            Dans le cas contraire, veuillez cliquer sur le lien ci-dessous. Celui-ci expirera après 1 heure.
            " . URL . "/reset-pass.php?token=$token";

            // On envoie le mail
            $sendmail->send();
            // echo "Mail envoyé";
            $_SESSION['message'][] = 'Si le compte concerné existe, vous recevrez un email de réinitialisation de mot de passe';
            header('Location: ' . URL);
            exit;

        }catch(Exception $e){
            // Ici le mail n'est pas parti
            echo 'Erreur : ' . $e->errorMessage();
        }

    }
}





?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css" integrity="sha384-VCmXjywReHh4PwowAiWNagnWcLhlEJLA5buUprzK8rxFgeH0kww/aWY76TfkUoSX" crossorigin="anonymous">

    <title>Mot de pass oublié</title>
  </head>
  <body>


<form class="form-inline" method="POST">

  <div class="form-group mx-sm-3 mb-2">
    <label for="email">Entrez eMail </label>
    <input type="email" class="form-control" id="email" name="email">

  </div>

  <button class="btn btn-primary mb-2">Réinitaliser mon mDp</button>

</form>



    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/js/bootstrap.min.js" integrity="sha384-XEerZL0cuoUbHE4nZReLT7nx9gQrQreJekYhJD9WNWhH8nEW+0c5qq7aIo2Wl30J" crossorigin="anonymous"></script>
  </body>
</html>