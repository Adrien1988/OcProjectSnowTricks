<?php

namespace App\Service;

/**
 * Service pour redimensionner (ou rogner) des images via l'extension GD.
 */
class ImageResizer
{


    /**
     * Redimensionne une image (JPEG/PNG) en la forçant dans $desiredWidth × $desiredHeight.
     *
     * @param string $sourcePath    Chemin absolu de l'image source
     * @param string $targetPath    Chemin de sortie de l'image redimensionnée
     * @param int    $desiredWidth  Largeur cible
     * @param int    $desiredHeight Hauteur cible
     *
     * @return bool true si la conversion réussit
     */
    public function resize(string $sourcePath, string $targetPath, int $desiredWidth, int $desiredHeight): bool
    {
        // Obtenir taille et type MIME
        [$width, $height, $type] = getimagesize($sourcePath);
        if (!$width || !$height) {
            return false;
        }

        // Créer la ressource PHP (GD) depuis le fichier
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceResource = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceResource = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceResource = imagecreatefromgif($sourcePath);
                break;
            default:
                return false; // type non géré
        }

        // Créer la ressource finale
        $destResource = imagecreatetruecolor($desiredWidth, $desiredHeight);

        // Redimensionnement
        imagecopyresampled(
            $destResource,
            $sourceResource,
            0,
            0,
            0,
            0,
            $desiredWidth,
            $desiredHeight,
            $width,
            $height
        );

        // Sauvegarde en JPEG (ou conditionnel si tu veux respecter le format)
        $quality = 85; // pour un bon compromis
        $ok = imagejpeg($destResource, $targetPath, $quality);

        // Libération
        imagedestroy($sourceResource);
        imagedestroy($destResource);

        return $ok;
    }


}
