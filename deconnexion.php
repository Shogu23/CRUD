<?php
require_once 'inc/header.php';

// On supprime la partie "user" de SESSION
unset($_SESSION['user']);
// On supprime le cookie
setcookie('remember', '', 
        [
            'path' => '/CRUD',
            'expires' => 1
        ]);


header('Location:'.URL);
