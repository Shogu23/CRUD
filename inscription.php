<?php
require_once 'inc/header.php';

use PHPMailer\PHPMailer\Exception;

//On vérifie que $_POST n'est pas vide
if(!empty($_POST)){
    // Ici on a reçu des données de formulaire
    // On vérifie que tout les champs obligatoires sont remplis
    if(
        isset($_POST['formnom']) && !empty($_POST['formnom'])
        && isset($_POST['formmail']) && !empty($_POST['formmail'])
        && isset($_POST['formphone']) && !empty($_POST['formphone'])
        && isset($_POST['formpass']) && !empty($_POST['formpass'])
        && isset($_POST['formpassverif']) && !empty($_POST['formpassverif'])
    ){
        // Tous les champs obligatoires sont remplis
        // On récupère et on nettoie les données
        $nomusers = strip_tags($_POST['formnom']);

        // On traite l'e-mail
        if(!filter_var($_POST['formmail'], FILTER_VALIDATE_EMAIL)){
            // L'e-mail est invalide
            $_SESSION['message'][] = "Email invalide";
        }else{
            // L'e-mail est valide
            $mailusers = $_POST['formmail'];
        }

        $phoneusers = strip_tags($_POST['formphone']);

        // On verifie si les MDP sont différents
        if($_POST['formpass'] != $_POST['formpassverif']){
            $_SESSION['message'][] = "Mots de pass différents";
        }else{
            // On chiffre le MDP
            $passusers = password_hash($_POST['formpass'], PASSWORD_ARGON2ID);
        }

        // Si il y a des messages d'erreur, on redirige
        if(!empty($_SESSION['message'])){
            header('Location: inscription.php');
            exit;
        }

        // Le formulaire est complet et les données "nettoyées"
        // On peut inscrire l'utilisateur
        
        // On se connecte à la base
        require_once 'inc/connect.php';
        
        // On ecrit la requete
        $sql = "INSERT INTO `users` (`email`, `password`, `name`, `phone`) VALUES (:mailusers, :password, :nomusers, :phoneusers); ";
        
        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':nomusers', $nomusers, PDO::PARAM_STR);;
        $query->bindValue(':mailusers', $mailusers, PDO::PARAM_STR);;
        $query->bindValue(':phoneusers', $phoneusers, PDO::PARAM_STR);;
        $query->bindValue(':password', $passusers, PDO::PARAM_STR);;

        // On execute la requête
        $query->execute();

        // On récupère l'id de l'utilisateur dans $num
        $num = $db->lastInsertId();

        $_SESSION['message'][] = "Vous êtes inscrit(e) sous le numéro $num";

        require_once 'inc/config-mail.php';
        
        // Ce fichier enverra un mail dès son chargement
        try{
            // On définit l'expéditeur du mail
            $sendmail->setFrom('no-reply@mondommaine.fr', 'Blog');

            // On définit le/les destinataire(s)
            $sendmail->addAddress($mailusers, $nomusers);

            // On définit le sujet du mail
            $sendmail->Subject = 'Confirmation d\'inscription';

            // On active le HTML
            $sendmail->isHTML();

            // On écrit le contenu du mail
            // En HTML
            $sendmail->Body = "<h1>Message de Blog</h1>
                               <p>Félicitation $nomusers, vous êtes désormais inscrit.</p>";

            // En texte brut
            $sendmail->AltBody = "L'user \"$nomusers\" viens d'être inscrit";

            // On envoie le mail
            $sendmail->send();
            // echo "Mail envoyé";

        }catch(Exception $e){
            // Ici le mail n'est pas parti
            echo 'Erreur : ' . $e->errorMessage();
        }

    }else{
        // Au moins 1 champs est invalide
        $_SESSION['message'][] = "Le formulaire est incomplet";
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <title>Inscription</title>
</head>

<body>

    <h1>INSCRIPTION</h1>

    <?php
    if(isset($_SESSION['message']) && !empty($_SESSION['message'])){
        foreach($_SESSION['message'] as $message){
            echo "<p>$message</p>";
        }
        unset($_SESSION['message']);
    }
    ?>

    <form method="post">
        <div class="form-group col-4">
            <label for="Name">Pseudo :</label>
            <input type="text" class="form-control" id="Name" name="formnom">
        </div>
        <div class="form-group col-4">
            <label for="Mail">Email :</label>
            <input type="email" class="form-control" id="Mail" name="formmail">
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group col-4">
            <label for="phone">Téléphone :</label>
            <input type="tel" class="form-control" id="phone" name="formphone">
        </div>
        <div class="form-group col-4">
            <label for="Passwrd">Mot de Passe :</label>
            <input type="password" class="form-control" id="Passwrd" name="formpass">
        </div>
        <div class="form-group col-4">
            <label for="VerifPasswrd">Mot de Passe :</label>
            <input type="password" class="form-control" id="VerifPasswrd" name="formpassverif">
        </div>
        <button class="btn btn-primary">Ajouter</button>
    </form>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous">
    </script>
</body>

</html>




