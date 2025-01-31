<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $targetDirectory;


    /**
     * Constructeur du service FileUploader.
     *
     * @param ParameterBagInterface $params Permet d'accéder aux paramètres Symfony
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->targetDirectory = $params->get('uploads_directory');
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
        } catch (\Exception $e) {
            return null;
        }
    }


    public function remove(string $filePath): bool
    {
        $absolutePath = $this->targetDirectory.'/'.basename($filePath);

        if (file_exists($absolutePath) && is_writable($absolutePath)) {
            return unlink($absolutePath);
        }

        return false;
    }


}
