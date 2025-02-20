<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Form\ImageType;
use App\Form\MainImageType;
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

            // On paramètre l’entité et on enregistre
            $image->setUrl('/uploads/'.$newFilename);
            $image->setFigure($figure);

            if ($figureService->saveEntity($image)) {
                $this->addFlash('success', 'L\'image a été ajoutée avec succès.');

                return $figureService->redirectToFigureDetail($figure);
            }

            // En cas d’échec de la sauvegarde (ex. exception en BDD), on informe l’utilisateur
            $this->addFlash('error', 'Erreur lors de la sauvegarde de l\'image.');
        }

        // Affichage des erreurs du formulaire, si soumis mais non valide
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire d\'image : '.implode(' - ', $errors));
            }
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
    #[Route('/edit/{id}', name: 'app_figure_edit_image', methods: ['GET', 'POST'])]
    public function editImage(
        Image $image,
        Request $request,
        FileUploader $fileUploader,
        FigureService $figureService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->denyAccessUnlessGranted('IMAGE_EDIT', $image);

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file')->getData();
            if ($uploadedFile) {
                $newFilename = $fileUploader->upload($uploadedFile);
                if (!$newFilename) {
                    $this->addFlash('error', 'Erreur lors de l\'upload.');

                    return $this->redirectToRoute('app_figure_edit', ['id' => $image->getFigure()->getId()]);
                }

                $image->setUrl('/uploads/'.$newFilename);
            }

            if ($figureService->saveEntity($image)) {
                $this->addFlash('success', 'Image modifiée avec succès.');

                return $this->redirectToRoute('app_figure_edit', ['id' => $image->getFigure()->getId()]);
            }

            $this->addFlash('error', 'Erreur lors de la modification de l\'image.');
        }

        // Affichage des erreurs du formulaire, si soumis mais non valide
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire d\'image : '.implode(' - ', $errors));
            }
        }

        return $this->redirectToRoute('app_figure_edit', ['id' => $image->getFigure()->getId()]);
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

        // Vérifie si l'utilisateur est bien le créateur de la figure
        $this->denyAccessUnlessGranted('IMAGE_DELETE', $image);

        if (!$this->isCsrfTokenValid('delete_image_'.$image->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        // Suppression du fichier image physique
        if (!$fileUploader->remove($image->getUrl())) {
            $this->addFlash('error', 'Erreur lors de la suppression du fichier image.');

            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        if ($figureService->saveEntity($image, true)) {
            $this->addFlash('success', 'Image supprimée avec succès.');

            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        $this->addFlash('error', 'Erreur lors de la suppression de l\'image.');

        return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
    }


    /**
     * Change l'image principale d'une figure.
     *
     * @param int           $id            L'identifiant de la figure
     * @param Request       $request       La requête HTTP contenant le formulaire
     * @param FigureService $figureService Service pour gérer les figures
     *
     * @return RedirectResponse Redirige vers la page d'édition de la figure
     */
    #[Route('/figure/{id}/set-main-image', name: 'app_figure_set_main_image', methods: ['POST'])]
    public function setMainImage(int $id, Request $request, FigureService $figureService): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureService->findFigureById($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(MainImageType::class, null, ['figure' => $figure]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageId = $form->get('mainImage')->getData();
            $referer = $request->headers->get('referer', $this->generateUrl('app_figure_detail', ['id' => $id]));

            foreach ($figure->getImages() as $image) {
                if ($image->getId() == $imageId) {
                    $figure->setMainImage($image);
                    break;
                }
            }

            if (!$figureService->saveEntity($figure)) {
                $this->addFlash('error', 'Erreur lors de la modification de l\'image principale.');

                return $this->redirect($referer);
            }

            $this->addFlash('success', 'Image principale modifiée avec succès.');

            return $this->redirect($referer);
        }

        // Affichage des erreurs du formulaire, si soumis mais non valide
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire de l\'image principale : '.implode(' - ', $errors));
            }
        }

        return $this->redirectToRoute('app_figure_detail', ['id' => $id]);
    }


    /**
     * Supprime l'image principale d'une figure.
     *
     * @param int           $id            L'identifiant de la figure
     * @param FigureService $figureService Service pour gérer les figures
     * @param Request       $request       La requête HTTP contenant le token CSRF
     *
     * @return RedirectResponse Redirection vers la page d'édition ou de détail de la figure
     */
    #[Route('/figure/{id}/remove-main-image', name: 'app_figure_remove_main_image', methods: ['POST'])]
    public function removeMainImage(int $id, FigureService $figureService, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureService->findFigureById($id);
        if (!$figure) {
            throw $this->createNotFoundException('Figure introuvable.');
        }

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('remove_main_image_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId()]);
        }

        // Supprimer l'image principale
        $figure->setMainImage(null);

        // On stocke le résultat de la sauvegarde
        $saveResult = $figureService->saveEntity($figure);

        // Message de succès si true
        if ($saveResult) {
            $this->addFlash('success', "L'image principale a été supprimée avec succès.");
        }

        // Message d’erreur si false
        if (!$saveResult) {
            $this->addFlash('error', "Erreur lors de la suppression de l'image principale.");
        }

        // Redirection sur la page actuelle (édition ou détail)
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'edit')) {
            return $this->redirectToRoute('app_figure_edit', ['id' => $figure->getId()]);
        }

        return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId()]);
    }


}
