<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $targetDirectory;


    /**
     * Constructeur du service FileUploader.
     */
    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }


    /**
     * Gère l'upload d'un fichier.
     *
     * @param UploadedFile $file Le fichier à uploader
     *
     * @return string|null Le nom du fichier généré ou null en cas d'erreur
     */
    public function upload(UploadedFile $file): ?string
    {
        $newFilename = uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->targetDirectory, $newFilename);

            return $newFilename;
        } catch (\Exception) {
            return null;
        }
    }


    /**
     * Supprime un fichier du serveur.
     *
     * @param string $filePath Le chemin du fichier à supprimer
     *
     * @return bool Retourne true si la suppression a réussi, sinon false
     */
    public function remove(string $filePath): bool
    {
        $basename = basename($filePath);
        $absolutePath = rtrim($this->targetDirectory, '/').'/'.$basename;

        if (file_exists($absolutePath) && is_writable($absolutePath)) {
            return unlink($absolutePath);
        }

        return false;
    }


    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }


}
