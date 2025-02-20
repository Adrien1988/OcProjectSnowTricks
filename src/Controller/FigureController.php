<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\ImageType;
use App\Form\MainImageType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Service\FigureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{


    /**
     * Modifie une figure existante avec une interface similaire à la vue détail.
     *
     * @param int           $id            L'identifiant de la figure à modifier
     * @param Request       $request       La requête HTTP contenant les données
     * @param FigureService $figureService Service pour gérer les figures
     *
     * @return Response La réponse HTTP avec le formulaire de modification
     */
    #[Route('/figure/edit/{id}', name: 'app_figure_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        FigureService $figureService,
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        // Récupérer la figure via la méthode privée
        $figure = $figureService->findFigureById($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        // Vérifie si l'utilisateur est bien le créateur
        $this->denyAccessUnlessGranted('FIGURE_EDIT', $figure);

        // Créer le formulaire de modification
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);

        // Création du formulaire pour modifier l'image principale
        $mainImageForm = $this->createForm(MainImageType::class, null, ['figure' => $figure])->createView();

        // Création des formulaires d'édition pour chaque image
        $imageForms = [];
        foreach ($figure->getImages() as $image) {
            $imageForms[$image->getId()] = $this->createForm(ImageType::class, $image)->createView();
        }

        // Génération des formulaires d'édition pour chaque vidéo
        $videoForms = [];
        foreach ($figure->getVideos() as $video) {
            $videoForms[$video->getId()] = $this->createForm(VideoType::class, $video)->createView();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if ($figureService->saveEntity($figure)) {
                $this->addFlash('success', 'La figure a été éditée avec succès.');

                return $figureService->redirectToFigureDetail($figure);
            }

            $this->addFlash('error', 'Une erreur est survenue lors de l’édition de la figure.');
        }

        // Si le formulaire de modification est soumis mais non valide, afficher les erreurs
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire de modification : '.implode(' - ', $errors));
            }
        }

        return $this->render(
            'figure/edit.html.twig',
            [
                'form'           => $form->createView(),
                'figure'         => $figure,
                'mainImageForm'  => $mainImageForm,
                'imageForms'     => $imageForms,
                'videoForms'     => $videoForms,
            ]
        );
    }


    /**
     * Supprime une figure existante.
     *
     * @param int           $id            L'identifiant de la figure
     *                                     à supprimer
     * @param FigureService $figureService Service pour gérer
     *                                     les figures
     * @param Request       $request       La requête HTTP contenant le
     *                                     token CSRF
     *
     * @return RedirectResponse La redirection vers la liste des figures
     */
    #[Route('/figure/delete/{id}', name: 'app_figure_delete', methods: ['POST'])]
    public function delete(
        int $id,
        FigureService $figureService,
        Request $request,
    ): RedirectResponse {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Récupérer la figure
        $figure = $figureService->findFigureById($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        // Vérifie si l'utilisateur est bien le créateur
        $this->denyAccessUnlessGranted('FIGURE_DELETE', $figure);

        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('delete_figure_'.$figure->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $figureService->redirectToFigureDetail($figure);
        }

        if ($figureService->saveEntity($figure, true)) {
            $this->addFlash('success', 'Figure supprimée avec succès.');

            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('error', 'La figure n’a pas pu être supprimée.');

        return $figureService->redirectToFigureDetail($figure);
    }


    /**
     * Affiche la page de détails d'une figure.
     *
     * @param int               $id                L'identifiant de la figure
     *                                             à modifier
     * @param FigureService     $figureService     Service pour gérer les figures
     * @param CommentRepository $commentRepository Le repository pour accéder aux commentaires
     * @param Request           $request           La
     *                                             requête
     *                                             HTTP
     *
     * @return Response la réponse HTTP avec le rendu de la page
     */
    #[Route('/figure/{id}', name: 'app_figure_detail', methods: ['GET'])]
    public function detail(int $id, FigureService $figureService, CommentRepository $commentRepository, Request $request): Response
    {
        $figure = $figureService->findFigureById($id);

        // Création du formulaire pour modifier l'image principale
        $mainImageForm = $this->createForm(MainImageType::class, null, ['figure' => $figure]);
        $mainImageForm->handleRequest($request);

        if ($mainImageForm->isSubmitted() && $mainImageForm->isValid()) {
            // Récupération de l'image sélectionnée
            $selectedImageId = $mainImageForm->get('mainImage')->getData();
            $selectedImage = null;

            foreach ($figure->getImages() as $image) {
                if ($image->getId() == $selectedImageId) {
                    $selectedImage = $image;
                    break;
                }
            }

            if ($selectedImage && $figureService->saveEntity($figure->setMainImage($selectedImage))) {
                $this->addFlash('success', 'Image principale mise à jour.');

                return $this->redirectToRoute('app_figure_detail', ['id' => $figure->getId()]);
            }

            // Sans else explicite
            $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de l’image principale.');
        }

        // Si le formulaire de l'image principale est soumis mais non valide, afficher les erreurs
        if ($mainImageForm->isSubmitted() && !$mainImageForm->isValid()) {
            $errors = [];
            foreach ($mainImageForm->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire de l\'image principale : '.implode(' - ', $errors));
            }
        }

        // Récupération de la pagination des commentaires
        $page = $request->query->getInt('page', 1); // Par défaut, page 1
        $commentsData = $commentRepository->findByFigureWithPagination($figure->getId(), $page, 10);

        return $this->render(
            'figure/detail.html.twig',
            [
                'figure'        => $figure,
                'comments'      => $commentsData['items'],
                'currentPage'   => $commentsData['currentPage'],
                'lastPage'      => $commentsData['lastPage'],
                'imageForm'     => $this->createForm(ImageType::class)->createView(),
                'videoForm'     => $this->createForm(VideoType::class)->createView(),
                'commentForm'   => $this->createForm(CommentType::class)->createView(),
                'mainImageForm' => $mainImageForm->createView(),
            ]
        );
    }


    /**
     * Ajoute un commentaire à une figure.
     *
     * Cette méthode permet aux utilisateurs connectés d'ajouter un commentaire
     * à une figure spécifique. Le commentaire est sauvegardé dans la base de données
     * et l'utilisateur est redirigé vers la page de la figure.
     *
     * @param int           $id            L'identifiant de la figure à modifier
     * @param Request       $request       La requête HTTP contenant les données
     *                                     du formulaire
     * @param FigureService $figureService Service pour gérer
     *                                     les figures
     *
     * @return RedirectResponse La redirection vers la page de détail de la figure
     */
    #[Route('/figure/{id}/add-comment', name: 'app_figure_add_comment', methods: ['POST'])]
    public function addComment(int $id, Request $request, FigureService $figureService): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureService->findFigureById($id);
        if (!$figure) {
            throw $this->createNotFoundException('Figure introuvable.');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setFigure($figure);

            if ($figureService->saveEntity($comment)) {
                $this->addFlash('success', 'Commentaire ajouté avec succès.');

                return $figureService->redirectToFigureDetail($figure);
            }

            $this->addFlash('error', 'Une erreur est survenue lors de l’ajout du commentaire.');
        }

        // Si le formulaire de commentaire est soumis mais non valide, afficher les erreurs
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            if (!empty($errors)) {
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire de commentaire : '.implode(' - ', $errors));
            }
        }

        return $figureService->redirectToFigureDetail($figure);

    }


    /**
     * Récupère et affiche les commentaires d'une figure avec pagination.
     *
     * Cette méthode permet de charger les commentaires associés à une figure,
     * triés du plus récent au plus ancien, avec une pagination (10 par page).
     *
     * @param int               $id                L'identifiant de la figure
     *                                             à modifier
     * @param Request           $request           La requête HTTP contenant les paramètres de pagination
     * @param FigureService     $figureService     Service pour gérer
     *                                             les figures
     * @param CommentRepository $commentRepository Le repository pour accéder aux commentaires
     *
     * @return Response La réponse HTTP contenant le rendu des commentaires
     */
    #[Route('/figure/{id}/comments', name: 'app_figure_comments', methods: ['GET'])]
    public function comments(
        int $id,
        Request $request,
        FigureService $figureService,
        CommentRepository $commentRepository,
    ): Response {
        $figure = $figureService->findFigureById($id);

        if (!$figure) {
            throw $this->createNotFoundException('Figure introuvable.');
        }

        $page = $request->query->getInt('page', 1);
        $comments = $commentRepository->findByFigureWithPagination($figure->getId(), $page, 10);

        return $this->render(
            'figure/comments.html.twig',
            [
                'figure'   => $figure,
                'comments' => $comments,
                'page'     => $page,
            ]
        );
    }


}
