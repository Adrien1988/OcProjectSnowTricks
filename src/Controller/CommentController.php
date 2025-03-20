<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{


    /**
     * Ajoute un commentaire à une figure (POST).
     * Utilise la logique de createAction() de l'abstract.
     *
     * @param int                    $id      L'identifiant de la figure
     * @param Request                $request La requête HTTP contenant le
     *                                        formulaire
     * @param EntityManagerInterface $em      Le gestionnaire d'entités
     *                                        Doctrine
     *
     * @return RedirectResponse|Response
     */
    #[Route('/figure/{id}/add-comment', name: 'app_figure_add_comment', methods: ['POST'])]
    public function addComment(int $id, Request $request, EntityManagerInterface $em): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $figure = $em->getRepository(Figure::class)->find($id);
        if (!$figure) {
            $this->addFlash('error', 'Figure introuvable.');

            return $this->redirectToRoute('app_home');
        }

        $comment = new Comment();
        $comment->setFigure($figure);
        $comment->setAuthor($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('succes', 'Commentaire ajouté avec succès.');
        } else {
            $this->addFlash('error', 'Impossible d’ajouter le commentaire.');
        }

        return $this->redirectToRoute(
            'app_figure_detail',
            [
                'id'   => $figure->getId(),
                'slug' => $figure->getSlug(),
            ]
        );
    }


}
