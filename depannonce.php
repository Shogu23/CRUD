<?php
require_once 'inc/header.php';

require_once 'inc/nav.php';

require_once 'inc/connect.php';

$sql = "SELECT a.*, c.`name` AS `catname`, d.`name` AS `depname`, 
        COUNT(a.`categories_id`) AS  `Nbr_de_Cat`  FROM `annonces` a 
        LEFT JOIN `categories` c ON a.`categories_id` = c.`id` 
        LEFT JOIN `departements` d ON d.`number` = a.`departements_number` 
        GROUP BY `depname`";

$query = $db->query($sql);

$listes = $query->fetchAll(PDO::FETCH_ASSOC);


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nombre de catg par deptm</title>
</head>
<body>

<table>
<h1>Liste de catégories par departement</h1>
<thead>
    <tr>
        <th>Département</th>
        <th>Nbr de catégories</th>
    </tr>
</thead>
<tbody>
    <?php
    foreach($listes as $liste):
?>
    <tr>
        <td><?= $liste['depname'] ?></td>
        
        <td style="text-align: center;"><?= $liste['Nbr_de_Cat'] ?></td>

    </tr>
    <?php
    endforeach;
?>
</tbody>
</table>


    
</body>
</html>