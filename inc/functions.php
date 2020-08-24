<?php
/**
 * Fonction qui formate une date donnée
 *
 * @param string $origDate
 * @return string
 */
function formatDate(string $origDate): string
{
    // On définit la langue du site
    setlocale(LC_TIME, 'FR_fr');

    // On formate la date dans la langue choisie
    $newdate = strftime('%A %d %B %Y - %T', strtotime($origDate));

    // On encode en UTF-8 pour gérer les caractères spéciaux
    $newdate = utf8_encode($newdate);

    // On retourne la date formatée
    return $newdate;
}


/**
 * Cette fonction renvoie un extrait du texte raccourci à la longueur demandée
 *
 * @param string $texte
 * @param integer $longueur
 * @return string
 */
function extrait(string $texte, int $longueur): string
{
    // On décode les caractères HTML
    $texte = htmlspecialchars_decode($texte);

    // On supprime le HTML
    $texte = strip_tags($texte);

    // On raccourcit le texte
    $texteReduit = mb_strimwidth($texte, 0, $longueur, '...');

    return $texteReduit;
}

/**
 * Cette fonction génère une miniature d'une image dans la taille demandée (carré) (PNG ou JPG)
 *
 * @param string $fichier Chemin complet du fichier
 * @param integer $taille Taille en pixels
 * @return boolean
 */
function mini(string $fichier, int $taille): bool 
{
    $dimensions = getimagesize($fichier);

    // On définit l'orientation et les décalages qui en découlent
    // On initialise les décalages
    $decalageX = $delacageY = 0;

    switch($dimensions[0] <=> $dimensions[1])
    {
        case -1: // Portrait
            $tailleCarre = $dimensions[0];
            $decalageY = ($dimensions[1] - $tailleCarre) / 2;
            break;
        
        case 0: // Carré
            $tailleCarre = $dimensions[0];
            break;

        case 1: // Paysage
            $tailleCarre = $dimensions[1];
            $decalageX = ($dimensions[0] - $tailleCarre) / 2;
    }
    // tailleCarre = suivant le dessin correspond a HauteCarré ou   LargeurCarré

        // On vérifie le type Mime de l'image
        switch($dimensions['mime'])
        {
            case 'image/png':
                $imageTemp = imagecreatefrompng($fichier);
                break;
            
            case 'image/jpeg':
                $imageTemp = imagecreatefromjpeg($fichier);
                break;

            default:
                return false;
        }
    

    // On crée une image temporaire en mémoire pour créer la copie
    $imageDest = imagecreatetruecolor($taille, $taille);

    // On copie la totalité de l'image source dans l'image de destination
    imagecopyresampled(
        $imageDest, // Image destination
        $imageTemp, // Image source
        0, // Point gauche de la zone de "collage"
        0, // Point supérieur de la zone de "collage"
        $decalageX, // Point gauche de la zone de "copie"
        $decalageY, // Point supérieur de la zone de "copie"
        $taille, // Largeur de la zone de "collage"
        $taille, // Hauteur de la zone de "collage"
        $tailleCarre, // Largeur de la zone de "copie"
        $tailleCarre // Hauteur de la zone de "copie"
    );

    // On démonte le nom de fichier
    $chemin = pathinfo($fichier, PATHINFO_DIRNAME);
    $nomFichier = pathinfo($fichier, PATHINFO_FILENAME);
    $extension = pathinfo($fichier, PATHINFO_EXTENSION);

    $nouveauFichier = "$chemin/$nomFichier-{$taille}x$taille.$extension";


    // On enregistre l'image sur le disque
    switch($dimensions['mime'])
    {
        case 'image/png':
            imagepng($imageDest, $nouveauFichier);
            break;
        
        case 'image/jpeg':
            imagejpeg($imageDest, $nouveauFichier);
    }

    // On detruit les images en mémoire
    imagedestroy($imageDest);
    imagedestroy($imageTemp);

    return true;

}




