<?php

use PHPMailer\PHPMailer\Exception;

require_once 'inc/header.php';
require_once 'inc/nav.php';




// Transforme une STRING ( chaine de carac "json" en tableau PHP )

if(!isset($_SESSION['user'])){
    header('Location:'.URL.'connexion.php');
}
$roles = json_decode($_SESSION['user']['roles']);  // On json_decode car 'user''roles' viens de la DB et est en json.
if(!in_array("ROLE_ADMIN", $roles)){
        header('Location:'.URL);
}

require_once 'inc/connect.php';

$sql = "SELECT * FROM `categories` ORDER BY `name` ASC";

$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM `departements` ORDER BY `name` ASC";

$query = $db->query($sql);

$departements = $query->fetchAll(PDO::FETCH_ASSOC);


// POST n'est pas vide, on vérifie TOUS les champs obligatoires
if(!empty($_POST)){
    // J'enregistre le POST de mon formulaire dans $_SESSION
    $_SESSION['form'] = $_POST;

    if(
        isset($_POST['formtitre']) && !empty($_POST['formtitre'])
        && isset($_POST['formcontent']) && !empty($_POST['formcontent'])
        && isset($_POST['formcat']) && !empty($_POST['formcat'])
        && isset($_POST['formprix']) && !empty($_POST['formprix'])
        && isset($_POST['formdep']) && !empty($_POST['formdep'])
        )        
    {
        // On récupere et on nettoie les données
        $anntitre = strip_tags($_POST['formtitre']);
        $anncontent = htmlspecialchars($_POST['formcontent']);
        
        $nomImage = null;

        if (is_float($_POST['formprix']) == true){
            $annprix = ($_POST['formprix']);
        }else{
            $_SESSION['message'][] = "Veuillez mettre un prix valide";
        }


        // On récupère et on stocke l'image si elle existe
        if(
            isset($_FILES['image']) && !empty($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE)
            {
            // On vérifie qu'on n'a pas d'erreur
            if($_FILES['image']['error'] != UPLOAD_ERR_OK){
                // On ajoute un message de session
                $_SESSION['message'][] = "Une erreur est survenue lors du transfert de fichier";
            }

            // On génère un nouveau nom de fichier
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $nomImage = md5(uniqid()).'.'.$extension;
            
            $goodExtensions = ['jpg', 'jpeg', 'jif', 'jfif', 'pjpeg', 'pjp', 'png'];
            $typemime = ['image/jpeg', 'image/png'];
            $type_file = $_FILES['image']['type'];

            // Vérif extension!            
            if (!in_array(strtolower($extension), $goodExtensions) || !in_array($type_file, $typemime))
            {
                $_SESSION['message'][] = "Désolé, only JPG, JPEG, PNG ou meme type, le reste c'est mort.";
            }
            
            // Je vérifie la taille de l'image ( limite a 1Mo ( 1024x1024))
            if ($_FILES['image']['size'] > 1048576){
                $_SESSION['message'][] = "Fichier trop volumineux, 1Mo max.";
            }

            if(isset($_SESSION['message']) && !empty($_SESSION['message'])){
                // Si au moins une erreur, on redirige vers le formulaire
                header('Location: ajout.php');
                exit;
            }
            
            // On transfère le fichier (le moveupload ( fichier source, fichier destination))
            if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/uploads/'.$nomImage))
            {
                // Transfert échoué
                $_SESSION['message'][] = "Erreur lors du transfert vers le dossier de destination";
                header('Location: ajout.php');
                exit;
            }
            
            mini(__DIR__.'/uploads/'.$nomImage, 200);
            mini(__DIR__.'/uploads/'.$nomImage, 300);    
        }
           

        // On ecrit la requete
        $sql = "INSERT INTO `annonces` (`title`, `content`, `users_id`, `categories_id`, `featured_image`, `departements_number`, `price`) 
                VALUES (:anntitre, :anncontent, :user, :anncat, :nomimage, :anndep, :annprix);";
        
        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':anntitre', $anntitre, PDO::PARAM_STR);
        $query->bindValue(':anncontent', $anncontent, PDO::PARAM_STR);
        $query->bindValue(':annprix', $annprix, PDO::PARAM_STR);
        $query->bindValue(':anncat', $_POST['formcat'], PDO::PARAM_INT);
        $query->bindValue(':user', $_SESSION['user']['id'], PDO::PARAM_INT);
        $query->bindValue(':nomimage', $nomImage, PDO::PARAM_STR);
        $query->bindValue(':anndep', $_POST['formdep'], PDO::PARAM_INT);
        
        // On execute la requête
        $query->execute();

        require_once 'inc/config-mail.php';
        
        // Ce fichier enverra un mail dès son chargement
        try{
            // On définit l'expéditeur du mail
            $sendmail->setFrom('no-reply@mondommaine.fr', 'Blog');

            // On définit le/les destinataire(s)
            $sendmail->addAddress('admin@mondomaine.fr', 'Admin');

            // On définit le sujet du mail
            $sendmail->Subject = 'Un article à été ajouté';

            // On active le HTML
            $sendmail->isHTML();

            // On écrit le contenu du mail
            // En HTML
            $sendmail->Body = "<h1>Message de Blog</h1>
                               <p>L'article \"$anntitre\" viens d'être ajouté par {$_SESSION['user']['nickname']}</p>";

            // En texte brut
            $sendmail->AltBody = "L'article \"$anntitre\" viens d'être ajouté par {$_SESSION['user']['nickname']}";

            // On envoie le mail
            $sendmail->send();
            // echo "Mail envoyé";

        }catch(Exception $e){
            // Ici le mail n'est pas parti
            echo 'Erreur : ' . $e->errorMessage();
        }

        header('Location:'.URL);
        exit;
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <title>Ajout d'article</title>
  </head>
  <body>
  <form enctype="multipart/form-data" method="post">
  <div class="form-group">
    <label for="Title">Titre</label>
    <input type="text" class="form-control" id="Title" name="formtitre"
    value="<?= isset($_SESSION['form']['formtitre']) ? $_SESSION['form']['formtitre'] : "" ?>">
  </div>
  <div class="form-group">
    <label for="Content">Contenu</label>
    <textarea class="form-control" id="Content" name="formcontent" rows="3"><?= !empty($_SESSION['form']['formcontent']) ? $_SESSION['form']['formcontent'] : "" ?></textarea>
  </div>
  <div class="form-group">
    <label for="Prix">Prix ?</label>
    <input type="text" class="form-control" id="Prix" name="formprix"
    value="<?= isset($_SESSION['form']['formprix']) ? $_SESSION['form']['formprix'] : "" ?>">
  </div>
  <div class="form-group">
    <label for="Categorie">Catégorie</label>
    <select class="form-control" id="Categorie" name="formcat" required>
    <option value="">-- Choisir une catégorie --</option>
    <?php foreach($categories as $categorie): ?>
                <option value="<?= $categorie['id'] ?>" <?php
                    if(isset($_SESSION['form']['formcat']) && $_SESSION['form']['formcat'] == $categorie['id'])
                    {
                        echo "selected";
                    }
                ?>>


                    <?= $categorie['name'] ?></option>
                <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label for="departement">Dép</label>
    <select class="form-control" id="departement" name="formdep" required>
    <option value="">-- Choisir un département --</option>
    <?php foreach($departements as $departement): ?>
                <option value="<?= $departement['number'] ?>" <?php
                    if(isset($_SESSION['form']['formdep']) && $_SESSION['form']['formdep'] == $departement['number'])
                    {
                        echo "selected";
                    }
                ?>>


                    <?= $departement['name'] ?></option>
                <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group">
    <label for="image">Image :</label>
    <input type="file" class="form-control-file" name ="image" id="image" accept="image/png, image/jpeg">
  </div>
  <div>
            <button>Ajouter</button>
        </div>
        <?php if(isset($_SESSION['message']) && !empty($_SESSION['message'])): 
            foreach($_SESSION['message'] as $message): ?>
        <div>
            <p><?= $message ?></p>
        </div>
        <?php endforeach; 
                unset($_SESSION['message']); 
                  endif; ?>
</form>
<?php unset($_SESSION['form']); ?>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  </body>
</html>





