<?php
require_once 'inc/header.php';

// On supprime la partie "user" de SESSION
unset($_SESSION['user']);

if(isset($_SERVER['HTTP_REFERER'])){
    header('Location: '.$_SERVER['HTTP_REFERER']);
}else{
    header('Location: '. URL);
}
// On supprime le cookie
setcookie('remember', '', 
        [
            'path' => '/CRUD',
            'expires' => 1
        ]);


header('Location:'.URL);
