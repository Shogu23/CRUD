<?php

if(isset($_SESSION['user'])){
    echo "Bonjour ".$_SESSION['user']['name']."<a href='".URL."/deconnexion.php'>Déconnexion</a>";
}else{
    echo '<a href="'.URL.'/connexion.php">Connexion</a> - <a href="'.URL.'">Inscription</a>'; 
}
