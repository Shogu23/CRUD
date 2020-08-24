<?php
// On se connecte a la base de données
    require_once '../inc/connect.php';

// On écrit la requete
    $sql = "DELETE FROM `categories` WHERE `categories`.`id` = :id;";

// On prepare la requete
    $query = $db->prepare($sql);

// On injecte les valeurs dans les parametres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

// On execute la requete
    $query->execute();

// On redirige
    header('Location: index.php');
?>