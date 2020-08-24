<?php



require_once 'inc/header.php';
// J'ajoute la nav
require_once 'inc/nav.php';



// On vérifie si on a un token dans l'url
if(isset($_GET['token']) && !empty($_GET['token'])){
    // On a un token
    // On récupere le token et on le nettoie
    $token = strip_tags($_GET['token']);

    // On se connecte a la DB
    require_once 'inc/connect.php';

    // On écrit la requête
    $sql = "SELECT * FROM `users` WHERE `reset_token` = :token;";

    // On prépare la requête
    $query = $db->prepare($sql);

    // On inject le token
    $query->bindValue(':token', $token, PDO::PARAM_STR);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // On a un utilisateur, ou pas!
    if(!$user){
        $_SESSION['message'][] = 'Aucun utilisateur ne correspond a ce token';
        header('Location: ' . URL);
        exit;
    }

    // On génère les dates actuelle et expiration
    $now = new DateTime();
    $expir = new DateTime($user['expiration_date']);

    // On compare pour savoir si le token a expiré
    if($expir < $now){
        // Le token a expiré
        // On l'efface de la base
        $sql = "UPDATE `users` SET `reset_token` = null, `expiration_date` = null WHERE `id` = {$user['id']};";

        // On exécute la requête
        $query = $db->query($sql);
        
        $_SESSION['message'][] = 'Désolé, le lien n\'est plus valide';
        header('Location: ' . URL);
        exit;
    }

    if(!empty($_POST)){
        //On verifie que les mots de pass sont identiques
        if(isset($_POST['pass1']) && !empty($_POST['pass1'])
        && isset($_POST['pass2']) && !empty($_POST['pass2'])){
            // Le formulaire est complet
            if($_POST['pass1'] === $_POST['pass2']){
                // Mots de passe identiques
                // On chiffre le MdP
                $pass = password_hash($_POST['pass1'], PASSWORD_ARGON2ID);

                // On met à jour la base de données
                $sql = "UPDATE `users` SET `password` = :pass, `reset_token` = null, `expiration_date` = null WHERE `id` = {$user['id']};";

                // On prépare la requête
                $query = $db->prepare($sql);

                // On injecte les valeurs dans les paramètres
                $query->bindValue(':pass', $pass, PDO::PARAM_STR);

                // On exécute la requête
                $query->execute();

                $_SESSION['message'][] = "MdP modifié";
                header('Location: ' . URL . '/connexion.php');
                exit;               

            }else{
                // Mots de passe différents
                $_SESSION['message'][] = "Les MdP sont différents";
            }
        }else{
            $_SESSION['message'][] = "Le formulaire est incomplet";
        }
    }

}else{
    // On n'a pas de token
    $_SESSION['message'][] = "Token missing";
    header('Location: ' . URL);
    exit;
}



?>



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <title>Reset Password</title>
  </head>
  <body>


  <?php if(isset($_SESSION['message']) && !empty($_SESSION['message'])): 
            foreach($_SESSION['message'] as $message): ?>
        <div>
            <p><?= $message ?></p>
        </div>
        <?php endforeach; 
                unset($_SESSION['message']); 
                  endif; ?>



    <form method="POST" class="form mw-30 mx-auto text-center" style="width: 24rem">

    <div class="form-group mt-3 mb-5">
    <label for="pass1">MdP</label>
    <input type="password" class="form-control" id="pass1" name="pass1">
    <label for="pass2">Confirm MdP</label>
    <input type="password" class="form-control" id="pass2" name="pass2">
  </div>




  <button class="btn btn-primary mb-2">Reset your password!</button>
</form>






    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  </body>
</html>