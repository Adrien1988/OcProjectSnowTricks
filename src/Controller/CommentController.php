<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Service\FigureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * Ajoute un commentaire à une figure.
     *
     * @param int           $id            L'identifiant de la figure à modifier
     * @param Request       $request       La requête HTTP contenant les données
     * @param FigureService $figureService Service pour gérer les figures
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

            try {
                $figureService->saveEntity($comment);
                $this->addFlash('success', 'Commentaire ajouté avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l’ajout du commentaire.');
            }
        }

        // Gestion des erreurs du formulaire de commentaire
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
     * Supprime un commentaire existant.
     *
     * @param int           $id            L'identifiant du commentaire à supprimer
     * @param FigureService $figureService Service pour gérer les figures
     * @param Request       $request       La requête HTTP contenant le token CSRF
     *
     * @return RedirectResponse La redirection vers la page de détail de la figure
     */
    #[Route('/figure/{figureId}/delete-comment/{id}', name: 'app_figure_delete_comment', methods: ['POST'])]
    public function deleteComment(int $id, int $figureId, FigureService $figureService, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $figureService->findFigureById($figureId);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');
            return $this->redirectToRoute('app_home');
        }

        $comment = $figure->getComments()->filter(function ($comment) use ($id) {
            return $comment->getId() === $id;
        })->first();

        if (!$comment) {
            $this->addFlash('error', 'Commentaire introuvable.');
            return $this->redirectToRoute('app_figure_detail', ['id' => $figureId]);
        }

        // Vérification des droits de l'utilisateur (ex : seul l'auteur peut supprimer son commentaire)
        if ($comment->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
            return $this->redirectToRoute('app_figure_detail', ['id' => $figureId]);
        }

        try {
            $figureService->saveEntity($comment, true); // Suppression via la méthode saveEntity avec un vrai "delete"
            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression du commentaire.');
        }

        return $this->redirectToRoute('app_figure_detail', ['id' => $figureId]);
    }
}
