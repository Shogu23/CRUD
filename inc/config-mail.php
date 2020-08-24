<?php
// PHPMailer est orienté Objet
// On appelle ses classes avec "use"
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On importe les fichiers de PHPMailer
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// On instancie PHPMailer (on le demarre)
$sendmail = new PHPMailer();

// On configure le serveur SMTP
$sendmail->isSMTP();

// On définit l'hôte du serveur
$sendmail->Host = 'localhost';

// On définit le port du serveur
$sendmail->Port = 1025;

// On définit le format utf-8 par defaut
$sendmail->CharSet = 'utf-8';