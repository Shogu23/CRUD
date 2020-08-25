<?php
require_once 'inc/header.php';
// On se connecte a la base de données
require_once 'inc/connect.php';

require_once 'inc/nav.php';

// Si tu veux ne selectionner que certaines parties a afficher
$sql = "SELECT a.*, c.`name` AS `catname`, u.`name`, d.`name` AS `depname` FROM `annonces` a 
        LEFT JOIN `categories` c ON a.`categories_id` = c.`id` 
        LEFT JOIN `users` u ON a.`users_id` = u.`id`
        LEFT JOIN `departements` d ON d.`number` = a.`departements_number` 
        ORDER BY a.`created_at` desc;";

$query = $db->query($sql);

$annonces = $query->fetchAll(PDO::FETCH_ASSOC);

?>


<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css"
        integrity="sha384-VCmXjywReHh4PwowAiWNagnWcLhlEJLA5buUprzK8rxFgeH0kww/aWY76TfkUoSX" crossorigin="anonymous">

    <title>Petites Annonces</title>
</head>

<body>
                  
    <div class="container">
        <div class="row">
            <?php foreach($annonces as $annonce): ?>
            <div class="col-md-6">

                <div class="h-100 card" style="width: 24rem;">

                    <?php if(!is_null($annonce['featured_image'])):
    // On fabrique le nom de l'image
    $extAnnonce = pathinfo($annonce['featured_image'], PATHINFO_EXTENSION);
    $nomAnnonce = pathinfo($annonce['featured_image'], PATHINFO_FILENAME);
    $imgAnnonce = "$nomAnnonce-300x300.$extAnnonce";
?>

                    <a href="annonces.php?id=<?= $annonce['id'] ?>"><img src="<?= URL . '/uploads/' . $imgAnnonce ?>" class="card-img-top"
                        alt="<?= $annonce['featured_image'] ?>"></a>

                    <?php endif; ?>

                    <div class="card-body">

                        <h5 class="card-title"><a
                                href="annonces.php?id=<?= $annonce['id'] ?>"><?= $annonce['title'] ?></a></h5>

                        <h6 class="card-subtitle mb-2 text-muted"> <?= $annonce['price'] ?>€</h6>

                        <h6 class="card-subtitle mb-2 text-muted"> <?= $annonce['name'] ?> from <?= $annonce['depname'] ?></h6>

                        <h6 class="card-subtitle mb-2 text-muted"> <?= formatDate($annonce['created_at']) ?></h6>

                        <h6 class="card-subtitle mb-2 text-muted"> <?= "Catégorie ".$annonce['catname'] ?></h6>

                        <p class="card-text"><?= extrait($annonce['content'], 150) ?></p>

                        <a href="modiftest.php?id=<?= $annonce['id'] ?>" class="btn btn-primary">Modifier</a>

                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>



    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/js/bootstrap.min.js"
        integrity="sha384-XEerZL0cuoUbHE4nZReLT7nx9gQrQreJekYhJD9WNWhH8nEW+0c5qq7aIo2Wl30J" crossorigin="anonymous">
    </script>
</body>

</html>