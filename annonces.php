<?php


require_once 'inc/header.php';

// On vérifie si on a un id dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){

    // On a un id, on va chercher l article dans la base
    // On se connecte
    require_once 'inc/connect.php';
    $sql = "SELECT a.*, c.`name` AS `catname`, u.`name`, d.`name` AS `depname` FROM `annonces` a 
    LEFT JOIN `categories` c ON a.`categories_id` = c.`id` 
    LEFT JOIN `users` u ON a.`users_id` = u.`id`
    LEFT JOIN `departements` d ON d.`number` = a.`departements_number`
    WHERE a.`id` = :id;";

    // On prepare la requete
    $query = $db->prepare($sql);

    //On accroche les valeurs aux parametres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    //On exécute la requete
    $query->execute();

    //On récupère les données
    $annonce = $query->fetch(PDO::FETCH_ASSOC);

    if(!$annonce){
        // l'article est vide (false)
        header('Location: index.php');
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

    <title>Annonces</title>
</head>

<body>

    <div class="jumbotron">
        <h1 class="display-4"><?= $annonce['title'] ?></h1>
        <p class="lead">Par <?= $annonce['name'] ?> from <?= $annonce['depname'] ?> dans <?= "Catégorie ".$annonce['catname']." le ".formatDate($annonce['created_at']) ?></p>
        <hr class="my-4">
        <p><?= $annonce['content'] ?></p>
        <?php if(!is_null($annonce['featured_image'])): ?>
            <p><img src="<?= URL . '/uploads/' . $annonce['featured_image'] ?>" alt="<?= $annonce['nickname'] ?>"></p>
        <?php endif; ?>
    </div>




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

</body>

</html>