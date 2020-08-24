<?php

require_once '../inc/header.php';


// On vérifie si on a un id dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){

    // On a un id, on va chercher la category dans la base
    // On se connecte
    require_once '../inc/connect.php';
    
    $sql = "SELECT * FROM `categories` WHERE `id` = :id";

    // On prepare la requete
    $query = $db->prepare($sql);

    //On accroche les valeurs aux parametres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    //On exécute la requete
    $query->execute();

    //On récupère les données
    $categorie = $query->fetch(PDO::FETCH_ASSOC);

    if(!$categorie){
        // category est vide (false)
        header('Location: index.php');
    }

    // Ici $personne contient un enregistrement de la base de données
    // On vérifie que POST contient des données


//On ajoute le nouveau client, AVANT d'aller chercher la liste ( le $sql = SELECT.... juste apres)
if(!empty($_POST)){
    // POST n'est pas vide, on vérifie TOUS les champs obligatoires
    if(
        isset($_POST['formnom']) && !empty($_POST['formnom'])
    ){
        //Form complet, tt les champs sont valides, on les proteges : (strip_tags() = No balises, htmlspechar = no <>, htmlentities = no <> no "" )
        $nomcat = strip_tags($_POST['formnom']);

        // On ecrit la requete
        $sql = "UPDATE `categories` SET `name` = :nomcat WHERE `categories`.`id` = {$categorie['id']}"; // aurait pu etre :id ***
        
        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        // $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT); Si :id a la place de {category['id']}
        $query->bindValue(':nomcat', $nomcat, PDO::PARAM_STR);

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


?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier</title>
</head>
<body>
    <h1>MODIFIER LES CATEGORIES</h1>
<form method="post">
        <div>
            <label for="Name">Nom</label>
            <input type="text" id="Name" name="formnom" value="<?= $categorie['name'] ?>">
        </div>
        <div>
            <button>Modifier</button>
        </div>
    </form>
</body>
</html>