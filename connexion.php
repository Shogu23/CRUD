<?php
require_once 'inc/header.php';
// On se connecte a la base de données
require_once 'inc/connect.php';


//On ajoute le nouveau client, AVANT d'aller chercher la liste ( le $sql = SELECT.... juste apres)
if(!empty($_POST)){
    // POST n'est pas vide, on vérifie TOUS les champs obligatoires
    if(
        isset($_POST['formmail']) && !empty($_POST['formmail'])
        && isset($_POST['formpass']) && !empty($_POST['formpass'])
    ){
        // On vérifie la validité de l'email
        if(!filter_var($_POST['formmail'], FILTER_VALIDATE_EMAIL)){
            die('email invalide');
            header ('Location: index.php');
        }else{
            $mailusers = $_POST['formmail'];
        }

        // On écrit la requete ( on va chercher dans la table users les infos)
        $sql = 'SELECT * FROM `users` WHERE `email` = :mailusers;';
      
        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':mailusers', $mailusers, PDO::PARAM_STR);;

        // On execute la requête
        $query->execute();

        // On récupere les données
        $user = $query->fetch(PDO::FETCH_ASSOC);
        
        if(!$user){
            die('Email et/ou mdp incorrect');
        } 

        if(password_verify($_POST['formpass'], $user['password'])){
            
            // On ouvre la session
            $_SESSION['user'] = [
              'id' => $user['id'],
              'name' => $user['name'],
              'email' => $user['email'],
              'roles' => $user['roles'],
            ];
            
            // On vérifie si la case est cochée
            if(isset($_POST['remember']) && $_POST['remember'] == true){
                // La case a été cochée
                // On génère un token
                $token = md5(uniqid());
                $expiration = date('Y-m-d H:i:s', strtotime("+1 year"));
        
                // On écrit la requête / On le stock en base
                $sql = "UPDATE `users` 
                        SET `remember_token` = '$token' 
                        WHERE `id` = '{$user['id']}' ";
 
                $query = $db->query($sql);

                // On le stock dans un cookie
                setcookie('remember', $token,
                [
                    'samesite' => 'Strict',
                    'expires' => strtotime('+1 year'),
                    'path' => '/blog'
                ]);

            }

            header('Location: ' . URL);
          
        }else{
            echo "Email et / ou mdp incorrect";
        }
    }else{
        $erreur = "Formulaire incomplet";
        echo $erreur;
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>Connexion</title>
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

    <h1>Veuillez vous connecter?</h1>
    <div class="container col-5 border border-primary">
        <form method="post">
            <div class="form-group">
                <label for="formmail">Email</label>
                <input type="email" id="Mail" name="formmail" class="form-control">
            </div>
            <div class="form-group">
                <label for="Passwrd">Mot de passe?</label>
                <input type="password" id="Passwrd" name="formpass" class="form-control">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember">
                <label class="form-check-label" for="remember" name="remember">Remember me?</label>
            </div>

            <p>
                <a href="oublie-pass.php">Mdp oublié</a>
            </p>
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
            integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
        </script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
            integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
        </script>
    </div>
</body>

</html>