<?php
require_once 'inc/header.php';

require_once 'inc/nav.php';

// On vérifie si on a un id dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){

    // On a un id, on va chercher la category dans la base
    // On se connecte
    require_once 'inc/connect.php';
    $sql = "SELECT * FROM `annonces` WHERE `id` = :id";
     
    // On prepare la requete
    $query = $db->prepare($sql);

    //On accroche les valeurs aux parametres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    //On exécute la requete
    $query->execute();

    //On récupère les données
    $annonce = $query->fetch(PDO::FETCH_ASSOC);

    if(!$annonce){
        // annonce est vide (false)
        header('Location: index.php');
        exit;
    }

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
        && isset($_FILES['image']) && !empty($_FILES['image'])
        && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE
        )        
    {
        // On récupere et on nettoie les données
        $anntitre = strip_tags($_POST['formtitre']);
        $anncontent = htmlspecialchars($_POST['formcontent']);
        
        if(!is_numeric($_POST['formprix'])){
            $_SESSION['message'][] = "Le prix est incorrect";
        }
        
        $nomImage = null;


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
        //    if (empty($nomImage)){
        //        $_SESSION['message'][] = "Veuillez joindre une image";
        //        header('Location: ajout.php');
        //        exit;
        //    }
            $sql = "UPDATE `annonces` SET `title` = :anntitre, `content` = :anncontent, `featured_image` = :nomimage, `departements_number` = :anndep, `categories_id` = :anncat, `price` = :annprix WHERE `annonces`.`id` = {$annonce['id']};";

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':anntitre', $anntitre, PDO::PARAM_STR);
        $query->bindValue(':anncontent', $anncontent, PDO::PARAM_STR);
        $query->bindValue(':anncat', $_POST['formcat'], PDO::PARAM_INT);
        $query->bindValue(':nomimage', $nomImage, PDO::PARAM_STR);
        $query->bindValue(':anndep', $_POST['formdep'], PDO::PARAM_STR);
        $query->bindValue(':annprix', $annprix, PDO::PARAM_STR);        
        
        
        // On execute la requête
        $query->execute();
        header('Location: index.php');
    }else{
        // Au moins 1 champs est invalide
        $erreur = "Le formulaire est incomplet";
    }
 
}


}else{
    // Pas d'id ou id vide, on retourne à la page index
    header('Location: index.php');
}


