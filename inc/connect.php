<?php
// On se connecte à la base de données
try {
    // On essaie de se connecter
    $dsn = 'mysql:dbname=annonces;host=localhost';

    $db = new PDO($dsn, 'charles', '4321');

    // echo "La connexion a fonctionné" ( confirmé que la connexion à fonctionner)
} catch (Exception $erreur) {
    // On gère l'échec du "try"
    echo "La connexion a échoué";
    die;
}
