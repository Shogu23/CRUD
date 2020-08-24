<?php
require_once '../inc/header.php';
// On se connecte a la base de données
require_once '../inc/connect.php';

//On ajoute le nouveau client, AVANT d'aller chercher la liste ( le $sql = SELECT.... juste apres)
if(!empty($_POST)){
    // POST n'est pas vide, on vérifie TOUS les champs obligatoires
    if(
        isset($_POST['formnom']) && !empty($_POST['formnom'])
    ){
        //TOUS les champs sont valides (strip_tags() = No balises, htmlspechar = no <>, htmlentities = no <> no "" )
        $nomcat = strip_tags($_POST['formnom']);

        // On ecrit la requete
        $sql = "INSERT INTO `categories` (`name`) VALUES (:nomcat);";
        
        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':nomcat', $nomcat, PDO::PARAM_STR);;

        // On execute la requête
        $query->execute();
    }else{
        // Au moins 1 champs est invalide
        $erreur = "Le formulaire est incomplet";
    }
 
}


$sql = 'SELECT * FROM `categories`;';

// On exécute notre requête
$query = $db->query($sql);

// On récupere les données
$liste = $query->fetchall(PDO::FETCH_ASSOC);

?>














<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout de Catégorie</title>
</head>
<body>

<table>
        <h1>Liste des catégories</h1>
        <thead>
            <tr>
                <th>Id</th>
                <th>Categories</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($liste as $categorie):
        ?>
            <tr>
                <td><?= $categorie['id'] ?></td>
                <td style="text-align: center;"><?= $categorie['name'] ?></td>
                <td><a href="modifier.php?id=<?= $categorie['id'] ?>">Modifier</a> <a href="delete.php?id=<?= $categorie['id'] ?>">Delete</a></td>
            </tr>
            <?php
            endforeach;
        ?>
        </tbody>
    </table>
    <form method="post">
        <h2>Ajouter une catégorie</h2>
        <div>
            <label for="Name">Nom</label>
            <input type="text" id="Name" name="formnom">
        </div>
        <div>
            <button>Ajouter</button>
        </div>
    </form>

    
</body>
</html>