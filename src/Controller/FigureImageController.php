<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Form\ImageType;
use App\Service\FigureService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/figure/image')]
class FigureImageController extends AbstractController
{


    /**
     * Ajoute une image à une figure.
     *
     * @param Figure        $figure        La figure associée à l'image
     * @param Request       $request       La requête HTTP contenant les données du formulaire
     * @param FileUploader  $fileUploader  Service de gestion des fichiers
     * @param FigureService $figureService Service pour gérer les figures
     *
     * @return RedirectResponse La redirection vers la page de détails de la figure
     */
    #[Route('/add/{id}', name: 'app_figure_add_image', methods: ['POST'])]
    public function addImage(Figure $figure, Request $request, FileUploader $fileUploader, FigureService $figureService): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ImageType::class, $image = new Image());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();

            // Si aucun fichier n’est transmis, on redirige immédiatement
            if (!$uploadedFile) {
                $this->addFlash('error', 'Aucun fichier sélectionné.');

                return $figureService->redirectToFigureDetail($figure);
            }

            // Tentative d’upload
            $newFilename = $fileUploader->upload($uploadedFile);
            if (!$newFilename) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');

                return $figureService->redirectToFigureDetail($figure);
            }

            // Tout est OK, on paramètre l’entité et on enregistre
            $image->setUrl('/uploads/'.$newFilename);
            $image->setFigure($figure);

            if ($figureService->saveEntity($image)) {
                $this->addFlash('success', 'L\'image a été ajoutée avec succès.');

                return $figureService->redirectToFigureDetail($figure);
            }

            // En cas d’échec de la sauvegarde (ex. exception en BDD), on informe l’utilisateur
            $this->addFlash('error', 'Erreur lors de la sauvegarde de l\'image.');
        }

        return $figureService->redirectToFigureDetail($figure);
    }


    /**
     * Modifie une image existante.
     *
     * @param Image         $image         L'image à modifier
     * @param Request       $request       La requête HTTP contenant les données du formulaire
     * @param FileUploader  $fileUploader  Service de gestion des fichiers
     * @param FigureService $figureService Service pour gérer les figures
     *
     * @return RedirectResponse La redirection vers la page de détails de la figure
     */
    #[Route('/edit/{id}', name: 'app_figure_edit_image', methods: ['POST'])]
    public function editImage(Image $image, Request $request, FileUploader $fileUploader, FigureService $figureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile);
                if ($newFilename) {
                    $image->setUrl('/uploads/'.$newFilename);
                } else {
                    $this->addFlash('error', 'Erreur lors de l\'upload.');

                    return $this->redirectToRoute('app_figure_edit_image', ['id' => $image->getId()]);
                }
            }

            if ($figureService->saveEntity($image)) {
                $this->addFlash('success', 'Image modifiée avec succès.');

                return $figureService->redirectToFigureDetail($image->getFigure());
            }

            $this->addFlash('error', 'Erreur lors de la modification de l\'image.');
        }

    }


    /**
     * Supprime une image existante.
     *
     * @param Image         $image         L'image à supprimer
     * @param Request       $request       La requête HTTP contenant le token CSRF
     * @param FigureService $figureService Service pour gérer les figures
     * @param FileUploader  $fileUploader  Service de gestion des fichiers
     *
     * @return RedirectResponse La redirection vers la page de détails de la figure
     */
    #[Route('/delete/{id}', name: 'app_figure_delete_image', methods: ['POST'])]
    public function deleteImage(Image $image, Request $request, FigureService $figureService, FileUploader $fileUploader): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $image->getFigure();

        if (!$this->isCsrfTokenValid('delete_image_'.$image->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $figureService->redirectToFigureDetail($figure);
        }

        // ✅ Suppression du fichier image physique
        if (!$fileUploader->remove($image->getUrl())) {
            $this->addFlash('error', 'Erreur lors de la suppression du fichier image.');

            return $figureService->redirectToFigureDetail($figure);
        }

        if ($figureService->saveEntity($image, true)) {
            $this->addFlash('success', 'Image supprimée avec succès.');

            return $figureService->redirectToFigureDetail($figure);
        }

        $this->addFlash('error', 'Erreur lors de la suppression de l\'image.');

        return $figureService->redirectToFigureDetail($figure);
    }


}
